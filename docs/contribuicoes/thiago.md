# Contribuição — Thiago (Usuários)

## Funcionalidade implementada
- Pesquisa de usuários por nome ou e-mail, com filtro adicional por papel (admin, organizador, torcedor).
- Tela com explicação dos três perfis de permissão (`_permissoes.blade.php`).
- Preservação dos filtros ao paginar a listagem (`withQueryString()`).
- Mensagem diferenciada quando não há usuários cadastrados x quando o filtro não retorna resultados.
- Proteção para que um administrador não consiga remover o próprio acesso de admin, mesmo havendo outros admins no sistema.

## Arquivos alterados/criados
- `app/Http/Requests/FiltroUsuarioRequest.php` (criado)
- `app/Http/Controllers/UsuarioController.php` (editado — método `index()` usando o request, e `alterarPapel()` com a proteção do próprio admin)
- `resources/views/usuarios/_filtros.blade.php` (criado)
- `resources/views/usuarios/_permissoes.blade.php` (criado)
- `resources/views/usuarios/index.blade.php` (editado)
- `tests/Feature/FiltroUsuariosTest.php` (criado)

## Testes executados
- `php artisan test --filter=FiltroUsuariosTest`
- `php artisan test --filter=ControleAcessoTest`
- `php artisan test --filter=AuthenticationTest`
- Testes manuais: filtro por nome/e-mail, filtro por papel, combinação dos dois, paginação mantendo filtro, acesso bloqueado para torcedor/organizador em `/usuarios`.
- Teste manual da proteção do admin: tentativa de alterar o próprio papel (bloqueada) e alteração do papel de outro usuário (funcionando normalmente).
- Conferida a responsividade em celular real via rede local.

## Dificuldades resolvidas
- A regra original só bloqueava a troca de papel quando o usuário era o **último** admin; foi ajustada para bloquear sempre que o próprio admin tentar mudar seu papel, independente de quantos outros admins existam.