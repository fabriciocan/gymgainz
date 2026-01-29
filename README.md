# Sistema LDAP - Projeto Base Nuxt

Sistema de gerenciamento de usuários e permissões integrado com LDAP, desenvolvido com Laravel 11 e Nuxt 3.

## 📋 Sobre o Projeto

Este é um sistema completo de autenticação e gerenciamento de usuários que integra com servidor LDAP (Active Directory). O sistema permite sincronização automática de usuários, gerenciamento de permissões baseado em roles e uma interface moderna e responsiva.

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 11** - Framework PHP
- **MySQL** - Banco de dados
- **Laravel Sanctum** - Autenticação API
- **LDAP** - Integração com Active Directory
- **Queue System** - Sistema de filas para processamento assíncrono

### Frontend
- **Nuxt 3** - Framework Vue.js
- **TypeScript** - Tipagem estática
- **Tailwind CSS** - Framework CSS
- **Pinia** - Gerenciamento de estado
- **Heroicons** - Ícones

## 📦 Funcionalidades

- ✅ Autenticação via LDAP/Active Directory
- ✅ Gerenciamento de usuários (CRUD completo)
- ✅ Sistema de permissões baseado em roles
- ✅ Sincronização automática com LDAP
- ✅ Criação de usuários externos (não-LDAP)
- ✅ Edição em massa de permissões
- ✅ Dashboard com informações do usuário
- ✅ Interface responsiva e moderna
- ✅ Dark mode
- ✅ Sistema de notificações
- ✅ Monitoramento de prazos
- ✅ Rate limiting de notificações

## 🔧 Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0
- Extensões PHP: ldap, pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json

## 📥 Instalação

### 1. Clone o repositório

```bash
git clone http://srvgit.prcidade.br/cti/projetobasenuxt.git
cd projetobasenuxt
```

### 2. Configuração do Backend (Laravel)

```bash
# Instalar dependências do Composer
composer install

# Copiar arquivo de ambiente
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Configurar banco de dados no .env
# DB_DATABASE=projetoBaseNuxt
# DB_USERNAME=root
# DB_PASSWORD=

# Executar migrations
php artisan migrate

# Executar seeders (opcional)
php artisan db:seed
```

### 3. Configuração do Frontend (Nuxt)

```bash
# Navegar para o diretório frontend
cd frontend

# Instalar dependências
npm install

# Copiar arquivo de ambiente
cp .env.example .env

# Configurar URL da API no .env
# NUXT_PUBLIC_API_BASE=http://localhost:8000
```

## 🔑 Configuração LDAP

Edite o arquivo `.env` na raiz do projeto:

```env
LDAP_ENABLED=true
LDAP_LOGGING=true
LDAP_HOST=10.51.10.46
LDAP_PORT=389
LDAP_BASE_DN="OU=Empregados,DC=prcidade,DC=br"
LDAP_USERNAME="seu-usuario@dominio.com.br"
LDAP_PASSWORD=sua-senha
LDAP_SSL=false
LDAP_TLS=false
LDAP_TIMEOUT=5
```

## 🚀 Executando o Projeto

### Backend (Laravel)

```bash
# Na raiz do projeto
php artisan serve
# API estará disponível em http://localhost:8000
```

### Frontend (Nuxt)

```bash
# No diretório frontend
npm run dev
# Interface estará disponível em http://localhost:3000
```

### Sistema de Filas (Opcional)

```bash
# Para processar notificações e jobs em segundo plano
php artisan queue:work
```

## 📁 Estrutura do Projeto

```
projetoBaseNuxt/
├── app/                    # Código Laravel
│   ├── Http/
│   │   ├── Controllers/   # Controllers da API
│   │   └── Middleware/    # Middlewares
│   ├── Models/            # Models Eloquent
│   └── Services/          # Serviços (LDAP, etc)
├── config/                # Configurações Laravel
├── database/
│   ├── migrations/        # Migrations do banco
│   └── seeders/          # Seeders
├── frontend/              # Aplicação Nuxt 3
│   ├── assets/           # Assets CSS/JS
│   ├── components/       # Componentes Vue
│   ├── composables/      # Composables Vue
│   ├── layouts/          # Layouts da aplicação
│   ├── pages/            # Páginas/Rotas
│   ├── stores/           # Stores Pinia
│   └── types/            # Tipos TypeScript
├── routes/
│   ├── api.php           # Rotas da API
│   └── web.php           # Rotas web
└── .env                  # Variáveis de ambiente
```

## 🔐 Autenticação

O sistema utiliza **Laravel Sanctum** para autenticação SPA. As credenciais são validadas via LDAP e os tokens são armazenados no frontend.

### Fluxo de Autenticação

1. Usuário faz login com credenciais LDAP
2. Backend valida no servidor LDAP
3. Se válido, cria/atualiza usuário no banco
4. Gera token Sanctum
5. Frontend armazena token e usa em requisições subsequentes

## 👥 Sistema de Permissões

O sistema utiliza um modelo de **roles** (funções):

- **admin** - Acesso total ao sistema
- **user** - Acesso básico
- **moderator** - Acesso intermediário (personalizado)

### Gerenciamento de Permissões

Acesse `/admin/users` para:
- Visualizar todos os usuários
- Adicionar/remover roles
- Edição em massa de permissões
- Ativar/desativar usuários
- Sincronizar com LDAP

## 📊 Dashboard

O dashboard exibe:
- Informações do usuário conectado
- Permissões atribuídas
- Status da conta (ativa/inativa)
- Tipo de usuário (LDAP/Externo)

## 🔄 Sincronização LDAP

Para sincronizar usuários do LDAP:

```bash
php artisan ldap:sync
```

Ou através da interface web em "Sincronizar LDAP".

## 🎨 Personalização

### Cores e Tema

Edite `frontend/tailwind.config.ts` para personalizar o tema:

```typescript
theme: {
  extend: {
    colors: {
      // Suas cores personalizadas
    }
  }
}
```

### Nome da Aplicação

Edite o `.env`:

```env
APP_NAME='Seu Sistema'
VITE_APP_NAME="${APP_NAME}"
```

## 🐛 Troubleshooting

### Erro de conexão LDAP

Verifique:
- Configurações do `.env` (host, porta, credenciais)
- Firewall não está bloqueando a porta 389
- Credenciais têm permissão de leitura no AD

### Erro de CORS

Adicione seu domínio frontend em:

```env
SANCTUM_STATEFUL_DOMAINS=localhost:3000,seudominio.com
SESSION_DOMAIN=.seudominio.com
```

### Erro ao fazer push

```bash
git pull origin main --rebase
git push origin main
```

## 📝 Variáveis de Ambiente Importantes

```env
# Aplicação
APP_NAME='Login PRC'
APP_URL=http://localhost

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=projetoBaseNuxt
DB_USERNAME=root
DB_PASSWORD=

# LDAP
LDAP_ENABLED=true
LDAP_HOST=10.51.10.46
LDAP_BASE_DN="OU=Empregados,DC=prcidade,DC=br"

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000

# Frontend
FRONTEND_URL=http://localhost:3000
```

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto é propriedade da Paranacidade.

## 👨‍💻 Desenvolvido por

CTI - Centro de Tecnologia da Informação
Paranacidade

## 📞 Suporte

Para suporte, entre em contato com a equipe de CTI.

---

**⚠️ Importante:** Nunca faça commit do arquivo `.env` que contém credenciais sensíveis!