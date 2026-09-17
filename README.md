# Bet dos Sapo

Projeto inicial em Laravel 13, com Vite e Tailwind CSS.

## Requisitos

- PHP 8.3 ou superior e Composer
- Node.js e npm
- Extensao pdo_pgsql habilitada no PHP e um banco PostgreSQL no Neon

## Preparar uma copia do repositorio

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
# Preencha DB_HOST, DB_DATABASE, DB_USERNAME e DB_PASSWORD no .env com os dados do Neon.
# Mantenha DB_SSLMODE=require.
php artisan config:clear
php artisan db:show
php artisan migrate
npm.cmd ci
npm.cmd run build
```

## Executar localmente

```powershell
php artisan serve
```

Abra http://127.0.0.1:8000. Para editar os estilos com atualizacao automatica, execute `npm.cmd run dev` em outro terminal.

## Validar

```powershell
php artisan test
npm.cmd run build
```

## Branches

`main`, `Luis`, `Nelson`, `Thiago` e `Joao`.

Para trabalhar em uma branch: `git switch Luis` (ou o nome correspondente).

O arquivo `.env`, `vendor` e `node_modules` ficam apenas no ambiente local. Nunca adicione a senha do Neon ao repositorio. O `.env.example` contem apenas um modelo sem credenciais.
# Conexão de apostas com o Neon pooler

Se o host do Neon usa `-pooler`, configure `DB_DISABLE_PREPARES=true` no `.env` local para usar o modo do PDO PostgreSQL compatível com esta instalação. Depois execute `php artisan config:clear`.

O banco recebe o perfil padrão `torcedor` pela migration `2026_09_16_014353_add_role_to_users_table.php`. Em uma instalação nova, execute `php artisan migrate`. O formulário público de cadastro não recebe o papel enviado pelo cliente; alterações para admin ou organizador devem ser feitas por um fluxo administrativo autorizado.
