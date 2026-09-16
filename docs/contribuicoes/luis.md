# Contribuição — Luis (Apostas e histórico)

## Funcionalidades implementadas
- Validação de registro de apostas em `ApostaRequest`, restrita ao perfil torcedor.
- Registro e cancelamento de palpites com créditos virtuais em `ApostaService`.
- Histórico restrito ao usuário autenticado, com filtro por pendente, ganha, perdida ou cancelada.
- Preservação do filtro na paginação do histórico.
- Formulário de filtros e mensagens distintas para histórico vazio e filtro sem resultados.
- Rotas autenticadas para listagem, histórico, registro e cancelamento de apostas.
- Central de palpites com botão de envio e links de paginação das partidas disponíveis.

## Arquivos da contribuição
- `app/Http/Requests/ApostaRequest.php`
- `app/Http/Controllers/ApostaController.php`
- `app/Models/Aposta.php`
- `app/Services/ApostaService.php`
- `database/migrations/2026_09_13_164353_create_apostas_table.php`
- `resources/views/apostas/index.blade.php`
- `resources/views/apostas/historico.blade.php`
- `resources/views/apostas/_filtros.blade.php`
- `routes/web.php` (rotas de apostas)
- `tests/Feature/FiltroApostasTest.php`
- `tests/Feature/Apostas/ApostaTest.php`

## Validação executada
- `php vendor/phpunit/phpunit/phpunit --filter="ApostaTest|FiltroApostasTest" --debug`: 13 testes aprovados, com 72 verificações.
- Os testes conferem registro, desconto de saldo, saldo insuficiente, partida finalizada, dados inválidos e bloqueio de registro para outros perfis.
- Conferem também cancelamento, devolução única de créditos, proteção contra cancelamento de apostas de outro usuário e bloqueio após o resultado.
- O histórico foi testado para os quatro status, isolamento entre usuários, lista vazia, status inválido e paginação sem alterar saldo ou registros.
- `php artisan route:list --path=apostas`: quatro rotas registradas.
- `npm run build`: concluído com sucesso em 16/09/2026.
- Renderização da view `apostas.index` com dados em memória: botão de envio e link da segunda página presentes. Esta verificação não substitui a conferência visual no navegador.
- Os testes usam SQLite em memória; não apagam nem migram o banco compartilhado do Neon.

## Dificuldades resolvidas
- Corrigido o uso de `validated()` no controller.
- Adicionado o retorno da view no método `historico()`.
- Registradas as rotas necessárias para os formulários e links das views.

## Pendências
- Integrar a estrutura de perfis de usuário. Ainda não existe a coluna `role`; os testes de registro atribuem o perfil apenas em memória. A aprovação desses testes não confirma o funcionamento com um usuário real do banco.
- Conferir visualmente as telas no navegador, incluindo mensagens de sucesso e erro e o uso no celular.
- Conferir a liquidação e o retorno de créditos junto com a simulação de partidas do João.
- Após a integração do grupo, executar a suíte completa e conferir os fluxos na interface.
