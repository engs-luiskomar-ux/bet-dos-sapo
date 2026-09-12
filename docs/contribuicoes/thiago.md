# Contribuição — Thiago (Gerenciamento de Usuários)

## Funcionalidade implementada
- Busca de usuários por nome ou e-mail na rota `/usuarios`.
- Tela explicativa dos papéis do sistema (Admin, Organizador, Torcedor).
- Instalação do Laravel Breeze (autenticação base do projeto).

## Arquivos alterados/criados
- `app/Http/Controllers/UsuarioController.php` (criado)
- `resources/views/usuarios/index.blade.php` (criado)
- `resources/views/usuarios/_permissoes.blade.php` (criado)
- `routes/web.php` (editado — rota `/usuarios`)

## Testes executados
- `tests/Feature/FiltroUsuariosTest.php`
  - Busca por nome
  - Busca por e-mail
  - Lista vazia quando não encontra
  - Redirecionamento quando não autenticado
- Resultado: 4 testes passando (10 assertions).

## Dificuldades resolvidas
- Erro "headers already sent" causado pela ausência da chave de aplicação (`APP_KEY`) — resolvido criando o `.env` e rodando `php artisan key:generate`.
- Testes falhando por driver SQLite ausente no PHP local — resolvido habilitando a extensão `pdo_sqlite` no `php.ini`.

## Pendente
- Filtro por papel (`role`) — depende da criação da coluna/estrutura de permissões, que é compartilhada entre as tarefas do grupo (Luis e Nelson também dependem dela). Aguardando alinhamento do grupo sobre quem vai criar essa base.