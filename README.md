# 📈 Pegaclick

**Pegaclick** é uma solução open source para monitoramento básico de acessos e cliques em sites.  
Ideal para quem busca uma alternativa simples e auto-hospedada ao Google Analytics ou Hotjar.

---

## 🎯 Funcionalidades

- Monitoramento de acessos às páginas
- Registro de cliques em elementos marcados
- Dashboard com estatísticas em tempo real
- Integração fácil com qualquer site (via script JS)
- Multi-usuário: cada usuário monitora seus próprios sites
- Geração automática de tokens de acesso para segurança
- Projeto leve, rápido e sem dependências pesadas

---

## 🚀 Como funciona

1. Cadastre seu site no painel Pegaclick.
2. Gere seu **Token de Acesso**.
3. Adicione o script Pegaclick no seu site:
    ```html
    <script src="https://seu-dominio.com/assets/pegaclick.js" defer></script>
    <script>
        Pegaclick.init('SEU_TOKEN_AQUI');
    </script>
    ```
4. (Opcional) Marque elementos para monitorar cliques:
    ```html
    <button data-monitorar-pegaclick="botao-enviar">Enviar</button>
    ```

Pronto! Seus acessos e cliques serão registrados no seu painel de controle.

---

## 🛠 Tecnologias usadas

- PHP 8+
- MySQL
- Bootstrap 5 (Frontend)
- Vanilla JS (Frontend)

---

## 📚 Documentação

A documentação básica já está embutida no próprio painel Pegaclick.  
(Se quiser contribuir para uma documentação mais detalhada, veja as [Issues abertas](https://github.com/SEU-USUARIO/pegaclick/issues)).

---

## 💡 Possibilidades futuras

- Heatmap de cliques
- Funil de conversão
- Integração com eventos personalizados
- Painel avançado de relatórios
- APIs públicas
- Plugin de integração para WordPress, WooCommerce etc.

Veja mais ideias nas [Issues abertas](https://github.com/SEU-USUARIO/pegaclick/issues)!

---

## 🤝 Como contribuir

1. Faça um fork do projeto.
2. Crie uma branch para sua feature/ajuste: `git checkout -b minha-feature`
3. Faça o commit: `git commit -m 'Minha nova feature'`
4. Push para seu fork: `git push origin minha-feature`
5. Crie um Pull Request!

---

## 📄 Licença

Este projeto está licenciado sob a licença MIT.  
Sinta-se livre para usar, modificar e distribuir!

---

## 💬 Contato

Desenvolvido por [Seu Nome Aqui].  
Se quiser conversar, sugerir ideias ou parcerias:
- LinkedIn: [Seu LinkedIn]
- GitHub: [Seu GitHub]
