# Contribuição — Luis (Apostas e histórico)

## Funcionalidades implementadas
- Validação de registro de apostas em `ApostaRequest`, restrita ao perfil torcedor.
- Registro e cancelamento de palpites com créditos virtuais em `ApostaService`.
- Histórico restrito ao usuário autenticado, com filtro por pendente, ganha, perdida ou cancelada.
- Preservação do filtro na paginação do histórico.
- Formulário de filtros e mensagens distintas para histórico vazio e filtro sem resultados.
- Rotas autenticadas para listagem, histórico, registro e cancelamento de apostas.
- Central de palpites com botão de envio e links de paginação das partidas disponíveis.
- Mensagens de sucesso e erros de validação na central de palpites e no histórico.

## Arquivos da contribuição
- `app/Http/Requests/ApostaRequest.php`
- `app/Http/Controllers/ApostaController.php`
- `app/Models/Aposta.php`
- `app/Services/ApostaService.php`
- `database/migrations/2026_09_13_164353_create_apostas_table.php`
- `resources/views/apostas/index.blade.php`
- `resources/views/apostas/historico.blade.php`
- `resources/views/apostas/_filtros.blade.php`
- `resources/views/apostas/_mensagens.blade.php`
- `routes/web.php` (rotas de apostas)
- `tests/Feature/FiltroApostasTest.php`
- `tests/Feature/Apostas/ApostaTest.php`

## Validação executada
- Após integrar a migration de perfis já presente na `main`: 55 testes aprovados, com 194 verificações. Os testes de apostas agora recarregam o usuário do banco e usam seu perfil persistido.
- Liquidação testada para mandante, empate e visitante, incluindo apostas ganhas, perdidas, placar, saldo e repetição sequencial sem pagamento duplicado.
- Conferência no navegador com conta temporária no Neon: login, registro de 100 créditos, saldo de 900, histórico, filtro Canceladas e filtro sem resultados. Cancelamento executado pelo service no Neon, com saldo restabelecido para 1.000; o diálogo do botão travou a automação e o clique completo ainda precisa de conferência manual.
- Histórico e formulário conferidos visualmente em tela de computador e no tamanho de celular (390 pixels).
- Suíte completa na branch `luis`, em 16/09/2026: `php vendor/phpunit/phpunit/phpunit --debug` — 54 testes aprovados, com 169 verificações. O build foi executado novamente após a inclusão das mensagens e também passou. Esta conferência abrange o código presente nesta branch, sem atualizar ou integrar novas alterações remotas do grupo.
- `php vendor/phpunit/phpunit/phpunit --filter="ApostaTest|FiltroApostasTest" --debug`: 13 testes aprovados, com 72 verificações.
- Os testes conferem registro, desconto de saldo, saldo insuficiente, partida finalizada, dados inválidos e bloqueio de registro para outros perfis.
- Conferem também cancelamento, devolução única de créditos, proteção contra cancelamento de apostas de outro usuário e bloqueio após o resultado.
- O histórico foi testado para os quatro status, isolamento entre usuários, lista vazia, status inválido e paginação sem alterar saldo ou registros.
- `php artisan route:list --path=apostas`: quatro rotas registradas.
- `npm run build`: concluído com sucesso em 16/09/2026.
- Renderização da view `apostas.index` com dados em memória: botão de envio e link da segunda página presentes. Esta verificação não substitui a conferência visual no navegador.
- Os testes usam SQLite em memória; não apagam nem migram o banco compartilhado do Neon.
- Renderização do componente de mensagens: confirmação, erro de validação e ausência de avisos quando não há mensagens conferidos em memória.

## Dificuldades resolvidas
- Sincronizada a migration `2026_09_16_014353_add_role_to_users_table.php` da `main`; ela já estava aplicada no Neon. Usuários novos recebem o perfil torcedor.
- Corrigida a falha de transação do driver PDO com o Neon pooler mediante `DB_DISABLE_PREPARES=true` no `.env` local. A configuração foi adicionada ao projeto, sem versionar credenciais.
- Corrigido o uso de `validated()` no controller.
- Adicionado o retorno da view no método `historico()`.
- Registradas as rotas necessárias para os formulários e links das views.

## Pendências
- Conferir manualmente o diálogo de confirmação do botão Cancelar palpite, que travou a automação do navegador.
- Conferir a liquidação e o retorno de créditos junto com a simulação de partidas do João.
- Após a integração do grupo, executar a suíte completa e conferir os fluxos na interface.
