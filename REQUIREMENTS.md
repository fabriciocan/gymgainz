# 🏋️ App de Progressão para Academia — Requisitos do Sistema

## Visão Geral

Aplicativo web PWA para acompanhamento de treinos, progressão de cargas e evolução corporal. Voltado para praticantes de musculação brasileiros. Modelo de negócio: assinatura mensal via AbacatePay.

---

## Stack Técnica

| Camada | Tecnologia |
|---|---|
| Frontend | Nuxt 4 + Tailwind CSS |
| PWA | @vite-pwa/nuxt |
| Backend | Laravel 12 |
| Banco de dados | PostgreSQL |
| Autenticação | Laravel Sanctum (já implementado) |
| Pagamentos | AbacatePay |
| Deploy | Hetzner VPS + Docker + Nginx + SSL |

> A autenticação (registro, login, logout, sanctum) já está implementada no projeto base. Não reimplementar.

---

## Modelo de Dados

### Tabelas

```sql
-- Treinos do usuário
workouts
  id, user_id, name, description, created_at, updated_at

-- Biblioteca de exercícios (global + do usuário)
exercises
  id, user_id (nullable — null = global), name, muscle_group, created_at

-- Exercícios dentro de um treino
workout_exercises
  id, workout_id, exercise_id, sets, reps, rest_seconds, order, created_at

-- Sessão de treino executada
training_sessions
  id, user_id, workout_id, date, duration_minutes, notes, created_at

-- Séries realizadas em cada sessão
session_sets
  id, session_id, workout_exercise_id, set_number, reps_done, weight_kg, created_at

-- Medidas corporais
body_measurements
  id, user_id, date, weight_kg, body_fat_pct, bicep_cm, chest_cm, waist_cm, hip_cm, thigh_cm, notes, created_at

-- Assinaturas
subscriptions
  id, user_id, status (active|cancelled|expired), plan, amount_cents,
  abacatepay_billing_id, current_period_start, current_period_end, created_at, updated_at
```

### Muscle Groups (enum ou tabela de referência)
`chest`, `back`, `shoulders`, `biceps`, `triceps`, `forearms`, `core`, `glutes`, `quadriceps`, `hamstrings`, `calves`, `full_body`

---

## Módulos do Sistema

### 1. Autenticação
- Já implementada no projeto base (Sanctum)
- Apenas garantir middleware `auth:sanctum` nas rotas protegidas
- Após login, verificar se usuário tem assinatura ativa — redirecionar para `/subscribe` se não tiver

---

### 2. Assinatura (Paywall)

**Fluxo:**
1. Usuário sem assinatura é redirecionado para `/subscribe`
2. Página exibe plano: **R$19,90/mês**
3. Botão "Assinar agora" chama endpoint que cria billing na AbacatePay
4. Usuário paga via PIX
5. Webhook da AbacatePay confirma pagamento → cria registro em `subscriptions`
6. Usuário liberado para usar o app

**Endpoints Laravel:**
```
POST /api/subscription/create     → cria cobrança recorrente na AbacatePay
POST /api/webhook/abacatepay      → recebe confirmação de pagamento (sem auth)
GET  /api/subscription/status     → retorna status da assinatura do usuário
```

**Regras:**
- Trial de 7 dias gratuito para novos usuários (campo `trial_ends_at` em `users`)
- Após trial, exige assinatura ativa
- Middleware `CheckSubscription` aplicado em todas as rotas protegidas exceto `/subscribe` e webhooks

---

### 3. Treinos (Workouts)

**CRUD completo de treinos do usuário.**

**Endpoints:**
```
GET    /api/workouts               → lista treinos do usuário
POST   /api/workouts               → cria treino
GET    /api/workouts/{id}          → detalhe com exercícios
PUT    /api/workouts/{id}          → edita treino
DELETE /api/workouts/{id}          → deleta treino

GET    /api/workouts/{id}/exercises           → lista exercícios do treino
POST   /api/workouts/{id}/exercises           → adiciona exercício ao treino
PUT    /api/workouts/{id}/exercises/{weId}    → edita séries/reps/ordem
DELETE /api/workouts/{id}/exercises/{weId}   → remove exercício do treino
POST   /api/workouts/{id}/exercises/reorder  → reordena exercícios (array de ids)
```

**Regras:**
- Usuário só acessa os próprios treinos
- Um treino pode ter N exercícios
- Cada exercício no treino tem: séries planejadas, reps planejadas, descanso em segundos

---

### 4. Exercícios (Exercises)

**Biblioteca de exercícios.**

**Endpoints:**
```
GET  /api/exercises                → lista exercícios globais + do usuário
POST /api/exercises                → cria exercício personalizado
PUT  /api/exercises/{id}           → edita (somente exercícios do próprio usuário)
DELETE /api/exercises/{id}         → deleta (somente exercícios do próprio usuário)
```

**Regras:**
- Exercícios globais (`user_id = null`) são pré-cadastrados e visíveis para todos
- Usuário pode criar exercícios personalizados
- Busca por nome e filtro por grupo muscular

**Seed de exercícios globais (mínimo 30):**
Supino reto, Supino inclinado, Crucifixo, Peck deck, Pull-down, Remada curvada, Remada unilateral, Puxada frontal, Desenvolvimento, Elevação lateral, Rosca direta, Rosca alternada, Tríceps corda, Tríceps testa, Agachamento, Leg press, Cadeira extensora, Cadeira flexora, Stiff, Avanço, Panturrilha em pé, Panturrilha sentado, Abdominal crunch, Prancha, Remada serrote, Barra fixa, Flexão de braço, Afundo, Hip thrust, Deadlift.

---

### 5. Sessão de Treino (Training Session)

**Core do produto. Usuário executa o treino registrando o que fez.**

**Endpoints:**
```
GET  /api/sessions                          → histórico de sessões
POST /api/sessions                          → inicia sessão (workout_id, date)
GET  /api/sessions/{id}                     → detalhe da sessão com sets
POST /api/sessions/{id}/sets               → registra set (exercise_id, set_number, reps_done, weight_kg)
PUT  /api/sessions/{id}/sets/{setId}       → edita set
DELETE /api/sessions/{id}/sets/{setId}     → remove set
PUT  /api/sessions/{id}/finish             → finaliza sessão (salva duration_minutes, notes)
GET  /api/sessions/{id}/previous           → retorna a sessão anterior com o mesmo workout_id
```

**Regras:**
- Ao abrir uma sessão, buscar automaticamente a sessão anterior do mesmo treino para exibir as cargas anteriores como referência
- Exibir carga anterior ao lado do campo de input da nova carga
- Se carga atual > carga anterior → exibir ícone de PR (Personal Record)

---

### 6. Progressão de Carga

**Gráfico de evolução por exercício.**

**Endpoints:**
```
GET /api/progress/exercise/{exerciseId}     → histórico de peso máximo por sessão
GET /api/progress/exercise/{exerciseId}/volume → volume total por sessão (séries × reps × peso)
```

**Resposta esperada:**
```json
{
  "exercise": { "id": 1, "name": "Supino Reto" },
  "data": [
    { "date": "2025-03-01", "max_weight": 60, "volume": 2400 },
    { "date": "2025-03-08", "max_weight": 62.5, "volume": 2500 }
  ]
}
```

**Regras:**
- Mínimo 2 pontos de dados para exibir gráfico
- Período filtrável: 30 dias, 90 dias, 6 meses, 1 ano, tudo

---

### 7. Medidas Corporais (Body Measurements)

**Endpoints:**
```
GET    /api/measurements            → histórico de medidas
POST   /api/measurements            → registra medida
PUT    /api/measurements/{id}       → edita
DELETE /api/measurements/{id}       → deleta
GET    /api/measurements/latest     → última medida registrada
```

**Regras:**
- Todos os campos são opcionais exceto `date`
- Exibir evolução de cada métrica em gráfico separado
- Destacar variação desde o início e variação nos últimos 30 dias

---

### 8. Dashboard

**Tela inicial após login.**

**Endpoint:**
```
GET /api/dashboard
```

**Resposta esperada:**
```json
{
  "last_session": { "date": "...", "workout_name": "...", "duration_minutes": 55 },
  "streak": { "current": 4, "best": 12 },
  "weekly_sessions": 3,
  "latest_weight": 82.5,
  "weight_change_30d": -1.2,
  "total_sessions": 47,
  "suggested_workout": { "id": 2, "name": "Treino B" }
}
```

**Regras de streak:**
- Conta dias consecutivos com pelo menos 1 sessão registrada
- Reinicia se passar 2 dias sem sessão

**Sugestão de treino:**
- Sugere o treino que foi feito há mais tempo (rodízio automático entre A, B, C)

---

## Páginas Nuxt

```
/                     → redirect para /dashboard ou /login
/login                → login
/register             → cadastro
/subscribe            → paywall (plano + PIX)
/dashboard            → tela inicial
/workouts             → lista de treinos
/workouts/new         → criar treino
/workouts/:id         → detalhe do treino + edição
/session/:workoutId   → executar treino (tela de sessão ativa)
/session/:id/history  → detalhe de sessão passada
/history              → histórico de sessões
/progress             → seletor de exercício + gráficos
/measurements         → medidas corporais + gráficos
/profile              → dados do usuário + gerenciar assinatura
```

---

## Componentes Nuxt (criar)

| Componente | Descrição |
|---|---|
| `WorkoutCard.vue` | Card de treino com nome, nº de exercícios, último uso |
| `ExerciseRow.vue` | Linha de exercício com séries/reps/descanso |
| `SessionSetInput.vue` | Input de série: reps + peso + botão confirmar + carga anterior |
| `ProgressChart.vue` | Gráfico de linha (usar Chart.js ou recharts) |
| `MeasurementChart.vue` | Gráfico de evolução de medidas |
| `StreakBadge.vue` | Badge com fogo + número de dias |
| `SubscribeCard.vue` | Card do plano com QR code PIX |
| `PRBadge.vue` | Badge de Personal Record (🏆) |

---

## PWA

Configurar `@vite-pwa/nuxt` com:

```js
// nuxt.config.ts
pwa: {
  manifest: {
    name: 'GymTrack',
    short_name: 'GymTrack',
    description: 'Acompanhe sua evolução na academia',
    theme_color: '#0f172a',
    background_color: '#0f172a',
    display: 'standalone',
    icons: [
      { src: '/icon-192.png', sizes: '192x192', type: 'image/png' },
      { src: '/icon-512.png', sizes: '512x512', type: 'image/png' }
    ]
  },
  workbox: {
    navigateFallback: '/',
    globPatterns: ['**/*.{js,css,html,png,svg,ico}']
  }
}
```

- Banner de instalação na primeira visita
- Funcionar offline nas páginas já visitadas
- Ícone na tela inicial igual a app nativo

---

## Integração AbacatePay

**Criar billing recorrente:**
```php
POST https://api.abacatepay.com/v1/billing/create
Authorization: Bearer {ABACATEPAY_API_KEY}

{
  "frequency": "MONTHLY",
  "methods": ["PIX"],
  "products": [{
    "externalId": "plan_basic",
    "name": "GymTrack Mensal",
    "description": "Acesso completo ao GymTrack",
    "quantity": 1,
    "price": 1990
  }],
  "customer": {
    "name": "...",
    "email": "...",
    "cellphone": "...",
    "taxId": "..."
  },
  "returnUrl": "https://seuapp.com/subscribe/success",
  "completionUrl": "https://seuapp.com/subscribe/success"
}
```

**Webhook (sem autenticação, validar via header secret):**
```php
// Eventos relevantes:
// BILLING_PAID     → ativar assinatura
// BILLING_EXPIRED  → cancelar assinatura
// BILLING_CANCELLED → cancelar assinatura

Route::post('/webhook/abacatepay', [WebhookController::class, 'handle']);
```

---

## Middleware Laravel

```php
// CheckSubscription.php
// Verifica se usuário tem:
// 1. Trial ativo (trial_ends_at > now()), OU
// 2. Assinatura com status = 'active' e current_period_end > now()
// Se não → retorna 403 com { "error": "subscription_required" }
// Frontend redireciona para /subscribe
```

---

## Variáveis de Ambiente

```env
# .env Laravel
ABACATEPAY_API_KEY=
ABACATEPAY_WEBHOOK_SECRET=
APP_URL=https://seuapp.com.br

# .env Nuxt
NUXT_PUBLIC_API_BASE=https://api.seuapp.com.br
```

---

## Design / UX

- Tema **dark** como padrão (academia = dark mode)
- Cor primária: azul/índigo (`#6366f1`)
- Fonte: Inter
- Mobile-first — maioria usa no celular durante o treino
- Tela de sessão ativa: simples, grande, sem distração — o usuário está suando
- Feedback visual ao bater PR (animação ou badge dourado)
- Inputs numéricos grandes na tela de sessão (fácil digitar com mão suada)

---

## Ordem de Implementação Recomendada

1. Migrations + Models + Seeders (exercícios globais)
2. Middleware CheckSubscription + rotas
3. Módulo de Assinatura (AbacatePay + webhook)
4. CRUD de Treinos e Exercícios
5. Sessão de Treino (core)
6. Dashboard + endpoints agregados
7. Progressão de Carga (gráficos)
8. Medidas Corporais
9. PWA config + ícones
10. Testes e ajustes de UX mobile

---

## Observações Finais

- Todos os endpoints da API retornam JSON
- Sempre retornar erros no formato `{ "message": "...", "errors": {} }`
- Usar Laravel Resources para todas as respostas
- Policies do Laravel para garantir que usuário só acessa seus próprios dados
- Índices no banco: `user_id` em todas as tabelas, `workout_id` em sessions, `session_id` em session_sets
- Não usar soft deletes por ora — manter simples
