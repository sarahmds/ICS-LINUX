# LojaInfoWeb — Projeto Sites Linux

Projeto acadêmico de uma loja virtual desenvolvido para a disciplina de Sites Linux.

Este repositório documenta a aplicação, a infraestrutura de servidores Linux, as configurações dos serviços e os testes realizados, seguindo os objetivos definidos pelo professor.

## Objetivo da aplicação

Desenvolver uma loja virtual hospedada em servidores Linux, utilizando Apache2, PHP e MariaDB/MySQL, com comunicação entre máquinas virtuais, resolução de domínio e operações no banco de dados através do PHP.

## Tecnologias utilizadas

- Linux
- VirtualBox
- Apache2
- PHP
- MariaDB/MySQL
- HTML, CSS e Bootstrap

## Arquitetura do projeto

A aplicação utiliza máquinas virtuais para representar os servidores e a máquina cliente. O servidor Web hospeda a aplicação por meio do Apache2, enquanto o PHP executa a lógica da aplicação e realiza operações no banco de dados MariaDB/MySQL, hospedado em um servidor separado.

A máquina cliente é utilizada para testar a comunicação entre as máquinas, a resolução do domínio e o acesso à aplicação pelo navegador.

## Estrutura do projeto

- `site-frontend/` — arquivos da vitrine pública.
- `site-backend/` — arquivos do painel administrativo.
- `confs-apache2/` — configurações dos VirtualHosts do Apache2.
- `confs-mysql/` — arquivos relacionados ao banco de dados.
- `docs/imagens/` — prints utilizados para documentar o projeto.

---

# Checklist de evidências da apresentação

## 1. Definição da aplicação

**Objetivo:** apresentar a proposta da aplicação desenvolvida no projeto Sites Linux.

**Descrição:** a LojaInfoWeb é uma loja virtual com uma vitrine pública e um painel administrativo para gerenciamento de produtos e pedidos.

**Print:** apresentação da página inicial da loja no navegador.

![Print 01 — Definição da aplicação](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

Abra a vitrine pública no navegador da máquina cliente e capture a página inicial da aplicação.

```text
Acesse o domínio configurado para a vitrine pública.
Exemplo: http://lojainfowebifrn.com
```

**O que demonstrar:** a finalidade da aplicação e sua interface pública.

## 2. Criação das máquinas virtuais no VirtualBox

**Objetivo:** criar as máquinas virtuais necessárias para o ambiente do projeto.

**Descrição:** as máquinas virtuais representam os servidores e a máquina cliente utilizados na implementação e nos testes.

**Print:** tela principal do VirtualBox mostrando as máquinas virtuais criadas.

![Print 02 — Máquinas virtuais](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

Abra o VirtualBox no computador e deixe visíveis os nomes e o estado das máquinas virtuais utilizadas no projeto.

```text
Não é necessário executar um comando.
O print deve ser obtido diretamente na interface do VirtualBox.
```

**O que demonstrar:** as máquinas virtuais criadas e sua finalidade no cenário.

## 3. Interconexão das máquinas virtuais

**Objetivo:** interconectar as máquinas servidoras e a máquina cliente.

**Descrição:** a comunicação entre as máquinas permite que os serviços do projeto sejam acessados e utilizados conforme a arquitetura definida.

**Print:** endereços IP e teste de comunicação entre as máquinas.

![Print 03 — Comunicação entre máquinas](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

Execute na máquina de origem:

```bash
ip addr
```

Depois, teste a comunicação com outra máquina:

```bash
ping -c 4 IP_DA_MAQUINA
```

Substitua `IP_DA_MAQUINA` pelo endereço IP real da máquina de destino.

**O que demonstrar:** os endereços IP configurados e o resultado do teste de conectividade.

## 4. Instalação do Apache2 e configuração do site

**Objetivo:** instalar o servidor Web Apache2 e configurar um site.

**Descrição:** o Apache2 recebe as requisições HTTP e disponibiliza os arquivos da aplicação. Os VirtualHosts permitem configurar os domínios e os diretórios atendidos pelo servidor.

**Print:** arquivo de configuração do VirtualHost e estado do Apache2.

![Print 04 — VirtualHost Apache2](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

Exiba o arquivo de configuração do VirtualHost utilizado no projeto:

```bash
sudo cat /etc/apache2/sites-available/lojainfowebifrn.backend.biz.conf
```

Verifique também o estado do serviço:

```bash
sudo systemctl status apache2
```

Se o nome do arquivo for diferente no servidor, utilize o caminho real da configuração.

**O que demonstrar:** as diretivas `ServerName`, `DocumentRoot` e outras configurações que realmente aparecem no arquivo, além do estado do serviço Apache2.

## 5. Configuração do DNS na máquina cliente

**Objetivo:** configurar o DNS na máquina cliente.

**Descrição:** a resolução de nomes permite que a máquina cliente encontre o endereço IP associado ao domínio utilizado no projeto.

**Print:** resultado da resolução do domínio na máquina cliente.

![Print 05 — Resolução DNS](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

Execute na máquina cliente Linux:

```bash
getent hosts lojainfowebifrn.com
```

Substitua o domínio pelo nome realmente configurado no ambiente.

**O que demonstrar:** o endereço IP retornado para o domínio. Se o comando não retornar um endereço, investigue a configuração antes de considerar essa etapa concluída.

## 6. Integração do PHP ao Apache2

**Objetivo:** integrar a linguagem de programação PHP ao Apache2.

**Descrição:** o PHP permite executar a lógica da aplicação no servidor Web, processando os arquivos PHP solicitados pelo navegador.

**Print:** versão do PHP e módulo PHP carregado no Apache2, quando essa for a configuração utilizada.

![Print 06 — PHP e Apache2](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

Verifique a versão do PHP:

```bash
php -v
```

Verifique os módulos carregados pelo Apache2:

```bash
sudo apache2ctl -M | grep php
```

Se o projeto utiliza PHP-FPM em vez do módulo PHP do Apache, verifique a configuração correspondente.

**O que demonstrar:** a instalação do PHP e sua integração efetiva com o servidor Web. A versão instalada, isoladamente, não comprova que o Apache consegue processar arquivos PHP.

## 7. Instalação e configuração do MariaDB/MySQL

**Objetivo:** instalar e configurar o servidor de banco de dados.

**Descrição:** o MariaDB/MySQL armazena os dados utilizados pela aplicação, permitindo que produtos e pedidos sejam consultados e manipulados.

**Print:** estado do serviço e estrutura do banco de dados do projeto.

![Print 07 — Servidor de banco de dados](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

No servidor de banco de dados, verifique o serviço:

```bash
sudo systemctl status mariadb
```

Acesse o banco:

```bash
sudo mariadb
```

Depois, execute no terminal do MariaDB:

```sql
SHOW DATABASES;
USE loja_db;
SHOW TABLES;
```

Substitua `loja_db` pelo nome real do banco, se necessário.

**O que demonstrar:** o serviço em funcionamento, a existência do banco do projeto e as tabelas efetivamente criadas.

## 8. Operações no banco de dados através do PHP

**Objetivo:** realizar operações no banco de dados utilizando PHP.

**Descrição:** a aplicação utiliza PHP para executar operações no banco de dados, como consultas e alterações nos registros de produtos e pedidos.

**Print:** arquivo responsável pela conexão com o banco de dados.

![Print 08 — Conexão PHP com MariaDB](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

Exiba o arquivo de conexão utilizado pelo backend:

```bash
sudo cat /var/www/html/lojainfowebifrn.com/site-backend/db.php
```

**Atenção:** oculte senhas e outras credenciais antes de capturar ou publicar a imagem.

**O que demonstrar:** como o PHP utiliza o endereço do servidor, o usuário, a senha e o nome do banco para estabelecer a conexão.

### Evidência complementar: operação SQL executada pelo PHP

Para comprovar que a aplicação realmente realiza operações no banco, identifique o arquivo PHP que consulta ou altera os registros.

**Print:** código responsável pela operação no banco.

![Print 08.1 — Operação PHP no banco](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

Liste os arquivos PHP do backend:

```bash
sudo find /var/www/html/lojainfowebifrn.com/site-backend -type f -name "*.php"
```

Depois, exiba o arquivo que implementa a operação:

```bash
sudo cat /CAMINHO/REAL/DO/ARQUIVO.php
```

Substitua o caminho pelo arquivo identificado no comando anterior.

**O que demonstrar:** o código que executa uma consulta, inserção, atualização ou exclusão de dados. A conexão, por si só, não comprova que uma operação foi realizada.

## 9. Entrega dos artefatos e apresentação do cenário

**Objetivo:** entregar os artefatos e apresentar o cenário proposto no Projeto Sites Linux.

**Descrição:** esta etapa reúne os arquivos da aplicação, as configurações dos serviços, a documentação e as evidências de funcionamento do ambiente.

**Print:** estrutura dos arquivos do projeto.

![Print 09 — Artefatos do projeto](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

Na máquina em que o repositório está armazenado, entre na pasta do projeto e execute:

```bash
tree -L 2
```

Se o comando `tree` não estiver instalado, utilize:

```bash
find . -maxdepth 2 -type f
```

**O que demonstrar:** a organização dos arquivos da aplicação, das configurações e da documentação.

### Evidência complementar: aplicação funcionando

**Print:** vitrine pública e painel administrativo no navegador.

![Print 09.1 — Funcionamento da aplicação](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

1. Abra o domínio da vitrine pública.
2. Capture a página com os produtos.
3. Acesse o painel administrativo.
4. Capture a tela de gerenciamento de produtos ou pedidos, sem expor credenciais.

**O que demonstrar:** o funcionamento da aplicação e as funcionalidades implementadas.

## 10. Portabilidade da aplicação para computação em nuvem

**Objetivo:** portar a aplicação desenvolvida no Projeto Sites Linux para um ambiente de computação em nuvem.

**Descrição:** esta etapa consiste em disponibilizar a aplicação em um ambiente de nuvem, considerando os componentes necessários para executar o site e acessar o banco de dados.

**Print:** evidência do ambiente de nuvem utilizado no projeto.

![Print 10 — Computação em nuvem](SUBSTITUA PELO LINK DA IMAGEM)

**Como obter o print:**

Após definir e configurar o ambiente de nuvem, registre a tela ou os comandos que comprovem a implantação real da aplicação.

```text
Os comandos desta etapa dependem do provedor e da arquitetura
de nuvem escolhidos para o projeto.
```

**O que demonstrar:** o ambiente de nuvem utilizado e a implantação da aplicação. Caso essa etapa ainda não tenha sido realizada, mantenha-a pendente e não apresente uma imagem ilustrativa como comprovação de implantação.

---

## Organização das imagens

Armazene os prints na pasta `docs/imagens/`, utilizando nomes que correspondam aos objetivos:

```text
docs/
└── imagens/
    ├── 01-definicao-aplicacao.png
    ├── 02-maquinas-virtuais.png
    ├── 03-interconexao-maquinas.png
    ├── 04-apache-virtualhost.png
    ├── 05-configuracao-dns.png
    ├── 06-integracao-php-apache.png
    ├── 07-servidor-mariadb.png
    ├── 08-conexao-php-banco.png
    ├── 08-1-operacao-php-banco.png
    ├── 09-artefatos-projeto.png
    ├── 09-1-aplicacao-funcionando.png
    └── 10-computacao-nuvem.png
```

Depois de anexar cada imagem ao repositório, substitua o link provisório pela referência correspondente. Se o print estiver dentro de `docs/imagens/`, você poderá utilizar diretamente o caminho relativo:

```markdown
![Print 04 — VirtualHost Apache2](docs/imagens/04-apache-virtualhost.png)
```

Após confirmar que a imagem está anexada e aparece corretamente no README, remova o bloco de comandos correspondente, mantendo o título, a descrição, a imagem e o que deve ser explicado na apresentação.

**Observação:** os dez objetivos seguem a lista do professor. As evidências complementares dos objetivos 8 e 9 ajudam a comprovar tanto a conexão quanto as operações reais e o funcionamento da aplicação.