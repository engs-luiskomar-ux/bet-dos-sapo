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


# Conexão de apostas com o Neon pooler

