# Trabalho de ICS — Ponto de parada

**Servidor WEB:** `10.25.1.48`

**Servidor DB:** `192.168.100.20`

**Frontend:** `lojainfowebifrn.com.br`

**Backend:** `lojainfowebifrn.backend.biz`

## O que já foi feito
- Apache com os dois VirtualHosts habilitados.
- Aplicação PHP funcionando e conectada ao banco MariaDB.
- CRUD de pedidos e produtos já testado anteriormente.
- Autenticação Basic configurada para o backend.
- Arquivo de senhas: `/etc/apache2/.htpasswd`.
- Arquivo `.htaccess`: `/var/www/html/lojainfowebifrn.com/site-backend/.htaccess`.
- Teste de acesso autenticado pelo Windows retornou `HTTP 200 OK`.
- Acesso por nome de domínio ainda depende de resolver o DNS ou configurar os nomes localmente no Windows.

## Próximas tarefas
1. Testar pedidos e produtos pelo navegador após o login.
2. Resolver o acesso aos dois sites pelo nome no navegador.
3. Conferir os estilos CSS e o frontend.
4. Revisar o repositório GitHub e os arquivos para entrega.
5. Preparar a apresentação e o diagrama de arquitetura.
6. Continuar a etapa de implantação em nuvem, se necessário.

**Importante:** não colocar senhas nem o arquivo `.htpasswd` no GitHub.