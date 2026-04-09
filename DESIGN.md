# 🎨 GymTrack — Design System & Requisitos Visuais

## Direção Estética

**Estilo:** Minimalismo editorial de alto contraste
**Referências visuais:** Arc'teryx, Notion, Linear, Apple Fitness+
**Conceito:** Um caderno de treinos premium digitalizado. Limpo, direto, sem ruído visual.
**O que o usuário vai lembrar:** "É o app mais limpo que já usei na academia"

---

## Paleta de Cores

```css
:root {
    /* Base */
    --color-bg: #ffffff;
    --color-bg-secondary: #f9f9f9;
    --color-bg-tertiary: #f2f2f2;

    /* Primária */
    --color-primary: #0a0a0a;
    --color-primary-hover: #1a1a1a;
    --color-secondary: #3d3d3d;
    --color-muted: #8a8a8a;
    --color-border: #e5e5e5;
    --color-border-strong: #cccccc;

    /* Feedback */
    --color-success: #16a34a;
    --color-success-bg: #f0fdf4;
    --color-error: #dc2626;
    --color-error-bg: #fef2f2;
    --color-warning: #d97706;
    --color-warning-bg: #fffbeb;

    /* PR / Destaque */
    --color-pr: #0a0a0a;
    --color-pr-bg: #f0f0f0;

    /* Overlay */
    --color-overlay: rgba(10, 10, 10, 0.6);
}
```

**Regra:** Nunca usar gradientes coloridos. Se precisar de gradiente, usar `#FFFFFF → #F2F2F2` (branco para cinza levíssimo).

---

## Tipografia

```css
/* Google Fonts */
@import url("https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap");

:root {
    --font-sans: "DM Sans", sans-serif;
    --font-mono: "DM Mono", monospace;

    /* Escala tipográfica */
    --text-xs: 0.75rem; /* 12px — labels, badges */
    --text-sm: 0.875rem; /* 14px — body secundário */
    --text-base: 1rem; /* 16px — body principal */
    --text-lg: 1.125rem; /* 18px — subtítulos */
    --text-xl: 1.25rem; /* 20px — títulos de seção */
    --text-2xl: 1.5rem; /* 24px — títulos de página */
    --text-3xl: 1.875rem; /* 30px — números grandes */
    --text-4xl: 2.25rem; /* 36px — hero numbers */
    --text-5xl: 3rem; /* 48px — display */

    /* Pesos */
    --font-light: 300;
    --font-regular: 400;
    --font-medium: 500;
    --font-semibold: 600;

    /* Line heights */
    --leading-tight: 1.2;
    --leading-normal: 1.5;
    --leading-relaxed: 1.7;

    /* Letter spacing */
    --tracking-tight: -0.02em;
    --tracking-normal: 0;
    --tracking-wide: 0.05em;
    --tracking-wider: 0.1em;
}
```

**Regras tipográficas:**

- Números grandes (peso, carga, PR) → `DM Mono`, tamanho grande, `tracking-tight`
- Labels de categoria → `DM Sans` uppercase, `text-xs`, `tracking-wider`, `color-muted`
- Títulos de página → `DM Sans`, `font-semibold`, `tracking-tight`
- Body → `DM Sans`, `font-regular`, `leading-relaxed`

---

## Espaçamento

```css
:root {
    --space-1: 4px;
    --space-2: 8px;
    --space-3: 12px;
    --space-4: 16px;
    --space-5: 20px;
    --space-6: 24px;
    --space-8: 32px;
    --space-10: 40px;
    --space-12: 48px;
    --space-16: 64px;
    --space-20: 80px;
    --space-24: 96px;
}

/* Padding interno de página (mobile) */
--page-padding: var(--space-5); /* 20px */

/* Padding interno de página (desktop) */
--page-padding-lg: var(--space-8); /* 32px */
```

**Regra:** Espaço generoso. O branco é parte do design, não espaço desperdiçado.

---

## Bordas e Sombras

```css
:root {
    /* Border radius */
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --radius-full: 9999px;

    /* Sombras (sutis, quase invisíveis) */
    --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.04);
    --shadow-sm: 0 1px 4px rgba(0, 0, 0, 0.06), 0 0 1px rgba(0, 0, 0, 0.04);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08), 0 0 1px rgba(0, 0, 0, 0.04);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.1), 0 0 1px rgba(0, 0, 0, 0.04);
    --shadow-xl: 0 16px 40px rgba(0, 0, 0, 0.12), 0 0 1px rgba(0, 0, 0, 0.04);
}
```

**Regra:** Nunca usar sombras coloridas. Sombras são preta com baixa opacidade. Cards têm `border: 1px solid var(--color-border)` com `shadow-xs` — não shadow pesada.

---

## Componentes

### Botões

```css
/* Primário */
.btn-primary {
    background: var(--color-primary);
    color: #ffffff;
    border-radius: var(--radius-md);
    font-family: var(--font-sans);
    font-size: var(--text-sm);
    font-weight: var(--font-medium);
    padding: 10px 20px;
    transition:
        background 150ms ease,
        transform 100ms ease;
}
.btn-primary:hover {
    background: var(--color-primary-hover);
}
.btn-primary:active {
    transform: scale(0.98);
}

/* Secundário */
.btn-secondary {
    background: transparent;
    color: var(--color-primary);
    border: 1px solid var(--color-border-strong);
    border-radius: var(--radius-md);
    padding: 10px 20px;
}
.btn-secondary:hover {
    background: var(--color-bg-secondary);
}

/* Ghost */
.btn-ghost {
    background: transparent;
    color: var(--color-secondary);
    padding: 10px 20px;
}
.btn-ghost:hover {
    background: var(--color-bg-tertiary);
}

/* Tamanhos */
.btn-sm {
    padding: 6px 14px;
    font-size: var(--text-xs);
}
.btn-lg {
    padding: 14px 28px;
    font-size: var(--text-base);
}
.btn-full {
    width: 100%;
    justify-content: center;
}
```

---

### Cards

```css
.card {
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--space-5);
    box-shadow: var(--shadow-xs);
}

/* Card interativo (hover) */
.card-interactive {
    cursor: pointer;
    transition:
        border-color 150ms ease,
        box-shadow 150ms ease,
        transform 150ms ease;
}
.card-interactive:hover {
    border-color: var(--color-border-strong);
    box-shadow: var(--shadow-sm);
    transform: translateY(-1px);
}
.card-interactive:active {
    transform: translateY(0);
}

/* Card destacado */
.card-filled {
    background: var(--color-bg-secondary);
    border-color: transparent;
}
```

---

### Inputs

```css
.input {
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    color: var(--color-primary);
    font-family: var(--font-sans);
    font-size: var(--text-base);
    padding: 10px 14px;
    width: 100%;
    transition:
        border-color 150ms ease,
        box-shadow 150ms ease;
    outline: none;
}
.input:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(10, 10, 10, 0.08);
}
.input::placeholder {
    color: var(--color-muted);
}

/* Input numérico grande (tela de sessão de treino) */
.input-number-lg {
    font-family: var(--font-mono);
    font-size: var(--text-3xl);
    font-weight: var(--font-medium);
    text-align: center;
    border: none;
    border-bottom: 2px solid var(--color-border);
    border-radius: 0;
    padding: var(--space-2) 0;
    background: transparent;
}
.input-number-lg:focus {
    border-bottom-color: var(--color-primary);
    box-shadow: none;
}
```

---

### Badge / Tags

```css
.badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-1);
    font-size: var(--text-xs);
    font-weight: var(--font-medium);
    padding: 3px 10px;
    border-radius: var(--radius-full);
    letter-spacing: var(--tracking-wide);
    text-transform: uppercase;
}
.badge-default {
    background: var(--color-bg-tertiary);
    color: var(--color-secondary);
}
.badge-success {
    background: var(--color-success-bg);
    color: var(--color-success);
}
.badge-pr {
    background: var(--color-primary);
    color: #ffffff;
}
.badge-muted {
    background: transparent;
    border: 1px solid var(--color-border);
    color: var(--color-muted);
}
```

---

### Navegação Mobile (Bottom Nav)

```css
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-top: 1px solid var(--color-border);
    display: flex;
    justify-content: space-around;
    padding: var(--space-2) 0 calc(var(--space-2) + env(safe-area-inset-bottom));
    z-index: 100;
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    padding: var(--space-2) var(--space-4);
    color: var(--color-muted);
    font-size: 10px;
    font-weight: var(--font-medium);
    letter-spacing: var(--tracking-wide);
    text-transform: uppercase;
    transition: color 150ms ease;
}
.nav-item.active {
    color: var(--color-primary);
}
.nav-item svg {
    width: 22px;
    height: 22px;
}
```

**Ícones:** usar `lucide-vue-next` — estilo fino, moderno, consistente.

---

### Divider com Label

```css
.divider-label {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    color: var(--color-muted);
    font-size: var(--text-xs);
    font-weight: var(--font-medium);
    letter-spacing: var(--tracking-wider);
    text-transform: uppercase;
}
.divider-label::before,
.divider-label::after {
    content: "";
    flex: 1;
    height: 1px;
    background: var(--color-border);
}
```

---

## Padrões de Layout

### Tela de Dashboard

```
┌─────────────────────────────┐
│  Bom dia, Fabrício  ·  ⚡4  │  ← saudação + streak
├─────────────────────────────┤
│                             │
│  [  CARD ÚLTIMO TREINO   ]  │  ← grande, com data e duração
│                             │
│  ESTA SEMANA                │  ← label uppercase muted
│  ●●●○○○○  3 de 5 dias       │
│                             │
│  EVOLUÇÃO                   │
│  Peso: 82.5kg  ↓1.2kg       │
│                             │
│  [  INICIAR TREINO B  ]     │  ← botão primário full width
│                             │
└─────────────────────────────┘
```

### Tela de Sessão Ativa (mais importante)

```
┌─────────────────────────────┐
│  ← Treino A          52:14  │  ← timer
├─────────────────────────────┤
│  Supino Reto                │  ← nome do exercício
│  Série 3 de 4               │  ← progresso
│                             │
│  ANTERIOR: 60kg × 10 reps   │  ← referência em muted
│                             │
│       [  62.5  ]  kg        │  ← input número grande
│       [  10   ]  reps       │  ← input número grande
│                             │
│   [ ✓ CONFIRMAR SÉRIE ]     │  ← botão primário
│                             │
│ ──────── SÉRIES ────────    │
│  ✓ S1  60kg × 10            │
│  ✓ S2  62.5kg × 10  🏆 PR   │
│  → S3  (atual)              │
│  ○ S4  pendente             │
└─────────────────────────────┘
```

### Cards de Treino

```
┌─────────────────────────────┐
│  Treino A                   │
│  PEITO · TRÍCEPS · OMBRO    │  ← label muted uppercase
│                             │
│  6 exercícios               │
│  Último: há 2 dias          │
│                       →     │  ← seta discreta
└─────────────────────────────┘
```

---

## Animações e Microinterações

```css
/* Transição padrão */
--transition-fast: 100ms ease;
--transition-base: 150ms ease;
--transition-slow: 300ms ease;
--transition-spring: 300ms cubic-bezier(0.34, 1.56, 0.64, 1);

/* Entrada de página */
@keyframes fadeSlideUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.page-enter {
    animation: fadeSlideUp 250ms ease forwards;
}

/* Entrada de item de lista (stagger) */
.list-item {
    opacity: 0;
    animation: fadeSlideUp 200ms ease forwards;
}
.list-item:nth-child(1) {
    animation-delay: 0ms;
}
.list-item:nth-child(2) {
    animation-delay: 50ms;
}
.list-item:nth-child(3) {
    animation-delay: 100ms;
}
.list-item:nth-child(4) {
    animation-delay: 150ms;
}

/* Confirmação de série */
@keyframes checkPop {
    0% {
        transform: scale(1);
    }
    40% {
        transform: scale(1.15);
    }
    100% {
        transform: scale(1);
    }
}
.set-confirmed {
    animation: checkPop 300ms cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* PR Badge */
@keyframes prReveal {
    0% {
        opacity: 0;
        transform: scale(0.5) rotate(-10deg);
    }
    60% {
        transform: scale(1.1) rotate(2deg);
    }
    100% {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }
}
.pr-badge {
    animation: prReveal 400ms cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Skeleton loading */
@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}
.skeleton {
    background: linear-gradient(90deg, #f2f2f2 25%, #e8e8e8 50%, #f2f2f2 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: var(--radius-sm);
}
```

---

## Gráficos

Usar **Chart.js** com configuração minimalista:

```js
// Configuração padrão dos gráficos
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: "#0A0A0A",
            titleColor: "#FFFFFF",
            bodyColor: "#CCCCCC",
            padding: 12,
            cornerRadius: 8,
            displayColors: false,
        },
    },
    scales: {
        x: {
            grid: { display: false },
            border: { display: false },
            ticks: {
                color: "#8A8A8A",
                font: { family: "DM Sans", size: 11 },
            },
        },
        y: {
            grid: { color: "#F2F2F2" },
            border: { display: false, dash: [4, 4] },
            ticks: {
                color: "#8A8A8A",
                font: { family: "DM Mono", size: 11 },
            },
        },
    },
    elements: {
        line: {
            borderColor: "#0A0A0A",
            borderWidth: 2,
            tension: 0.3,
            fill: true,
            backgroundColor: (ctx) => {
                const gradient = ctx.chart.ctx.createLinearGradient(
                    0,
                    0,
                    0,
                    200,
                );
                gradient.addColorStop(0, "rgba(10,10,10,0.08)");
                gradient.addColorStop(1, "rgba(10,10,10,0)");
                return gradient;
            },
        },
        point: {
            radius: 4,
            hoverRadius: 6,
            backgroundColor: "#FFFFFF",
            borderColor: "#0A0A0A",
            borderWidth: 2,
        },
    },
};
```

---

## Iconografia

**Biblioteca:** `lucide-vue-next`
**Tamanhos padrão:**

| Contexto                | Tamanho | Stroke |
| ----------------------- | ------- | ------ |
| Bottom nav              | 22px    | 1.5    |
| Botões inline           | 16px    | 1.5    |
| Títulos de seção        | 18px    | 1.5    |
| Feedback (sucesso/erro) | 20px    | 2      |
| Display / destaque      | 32px    | 1.5    |

**Ícones por seção:**

- Dashboard → `LayoutDashboard`
- Treinos → `Dumbbell`
- Histórico → `History`
- Progressão → `TrendingUp`
- Medidas → `Ruler`
- Perfil → `User`
- Iniciar treino → `Play`
- Confirmar série → `Check`
- PR → `Trophy`
- Streak → `Flame`

---

## Estado Vazio (Empty States)

Nunca deixar tela vazia sem contexto:

```
┌─────────────────────────────┐
│                             │
│         [ícone grande]      │
│                             │
│      Nenhum treino ainda    │  ← título
│   Crie seu primeiro treino  │  ← subtítulo muted
│   e comece a evoluir.       │
│                             │
│   [ + Criar treino ]        │  ← CTA
│                             │
└─────────────────────────────┘
```

Fundo: `--color-bg-secondary`, ícone: `color-muted`, tamanho 40px stroke 1.

---

## Tela de Assinatura (Paywall)

Design premium, não agressivo:

```
┌─────────────────────────────┐
│                             │
│  GymTrack                   │  ← logo/nome
│  Pro                        │  ← badge preto
│                             │
│  R$ 19,90                   │  ← número grande, DM Mono
│  /mês                       │  ← muted
│                             │
│  ✓ Treinos ilimitados       │
│  ✓ Histórico completo       │
│  ✓ Gráficos de evolução     │
│  ✓ Medidas corporais        │
│                             │
│  [  ASSINAR COM PIX  ]      │  ← botão primário full
│                             │
│  7 dias grátis, cancele     │  ← texto muted, xs
│  quando quiser              │
│                             │
└─────────────────────────────┘
```

---

## Responsividade

| Breakpoint | Largura    | Comportamento                                             |
| ---------- | ---------- | --------------------------------------------------------- |
| Mobile     | < 640px    | Layout base, bottom nav, padding 20px                     |
| Tablet     | 640–1024px | Layout base com sidebar colapsável                        |
| Desktop    | > 1024px   | Sidebar fixa 240px, conteúdo centralizado max-width 720px |

**Regra:** O produto é mobile-first. O usuário usa durante o treino, com a mão suada. Prioridade absoluta para:

- Inputs grandes e fáceis de tocar (mínimo 44px de altura)
- Botões com área de toque generosa
- Fontes legíveis com brilho de academia (pelo menos 16px no body)
- Nada que exija precisão de toque

---

## Regras de Ouro do Design

1. **Branco é o herói** — nunca poluir com elementos desnecessários
2. **Preto é a âncora** — usado com propósito, nunca decorativo
3. **Cinza é o suporte** — escala de cinzas faz todo o trabalho secundário
4. **Uma ação por tela** — o usuário nunca deve ficar em dúvida do que fazer
5. **Números em monospace** — todo número de dado (carga, peso, reps) usa `DM Mono`
6. **Sem sombras pesadas** — leveza visual acima de tudo
7. **Espaço é luxo** — margens generosas transmitem qualidade
8. **Animações são funcionais** — só animar quando add contexto ou feedback
9. **Labels sempre uppercase muted** — categorias, rótulos, metadados
10. **Consistência antes de criatividade** — sistema sempre vence improviso
