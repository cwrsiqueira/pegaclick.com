var Pegaclick = (function () {
    var token = null;

    function enviarEvento(tipo, elemento) {
        if (!token) {
            console.error('Pegaclick: Token não configurado.');
            return;
        }

        fetch('https://pegaclick.carlosdev.com.br/api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                token: token,
                pagina: window.location.pathname,
                evento: tipo,
                elemento: elemento
            })
        })
            .then(response => {
                if (response.status === 204) {
                    console.info('Pegaclick: evento ignorado (elemento não cadastrado ou acesso desabilitado).');
                    return; // Não tenta fazer .json()
                }
                return response.json();
            })
            .then(data => {
                if (data) {
                    // Opcional: console.log('Evento registrado:', data);
                }
            })
            .catch(error => {
                console.error('Pegaclick: erro ao enviar evento.', error);
            });
    }

    function init(tokenSite) {
        token = tokenSite;

        // Enviar evento de acesso ao inicializar
        enviarEvento('acesso', 'pagina');

        // Monitorar cliques
        document.querySelectorAll('[data-monitorar-pegaclick]').forEach(function (el) {
            el.addEventListener('click', function () {
                var nomeElemento = el.getAttribute('data-monitorar-pegaclick');
                enviarEvento('click', nomeElemento);
            });
        });
    }

    return {
        init: init
    };
})();
