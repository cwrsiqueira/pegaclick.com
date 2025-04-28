<?php
define('PEGACLICK', true);
require 'config.php';
verificaLogin();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Documentação - Pegaclick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">
        <h1 class="mb-4">📄 Documentação Pegaclick</h1>

        <div class="card p-4 bg-white mb-4">
            <h2 class="h4 mb-3">Como usar o Pegaclick no seu site</h2>

            <ol class="list-group list-group-numbered mb-4">
                <li class="list-group-item">
                    Cadastre seu site no Painel Pegaclick para gerar seu <strong>Token de Acesso</strong>.
                </li>
                <li class="list-group-item">
                    Cadastre todos os elementos que você deseja monitorar (botões, links, áreas clicáveis).<br>
                    Cada elemento deve ter um <code>nome único</code> e uma <code>descrição</code>.
                </li>
                <li class="list-group-item">
                    No seu site, adicione o seguinte código antes do fechamento da tag <code>&lt;/body&gt;</code>:
                    <pre><code>&lt;script src="https://pegaclick.carlosdev.com.br/assets/pegaclick.js" defer&gt;&lt;/script&gt;
&lt;script&gt;
  Pegaclick.init('SEU_TOKEN_AQUI');
&lt;/script&gt;</code></pre>
                </li>
                <li class="list-group-item">
                    Marque os elementos que você quer monitorar usando o atributo <code>data-monitorar-pegaclick</code> com o mesmo nome cadastrado.<br><br>Exemplo:
                    <pre><code>&lt;button data-monitorar-pegaclick="btn-enviar"&gt;Enviar&lt;/button&gt;</code></pre>
                </li>
                <li class="list-group-item">
                    O Pegaclick automaticamente enviará:
                    <ul>
                        <li><strong>Um evento de acesso</strong> toda vez que a página for carregada (sem precisar marcar manualmente).</li>
                        <li><strong>Um evento de clique</strong> sempre que o usuário clicar em elementos marcados corretamente.</li>
                    </ul>
                </li>
                <li class="list-group-item text-danger">
                    Importante:
                    <ul>
                        <li>Se o elemento clicado <strong>não estiver cadastrado</strong> no painel, o clique será <strong>ignorado</strong> (não registrado).</li>
                        <li>Cada vez que a página for recarregada (F5), um novo acesso será contado.</li>
                        <li>Em futuras versões será possível refinar o controle de sessões e acessos únicos.</li>
                    </ul>
                </li>
            </ol>

            <a href="index.php" class="btn btn-primary">⬅️ Voltar ao Painel</a>
        </div>
    </div>
</body>

</html>