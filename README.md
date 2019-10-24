# GERENCIAMENTO DE ENTRADAS & SAÍDAS PARA CONDOMÍNIOS

<section data-markdown>
  
  ![Screen 01](https://github.com/NathanaelCruz/images_resource_projects/blob/master/Images/screen_Cond_02.png)
  <img src="https://github.com/NathanaelCruz/images_resource_projects/blob/master/Images/screen_Cond_01.png" width="280"/>
  <img src="https://github.com/NathanaelCruz/images_resource_projects/blob/master/Images/screen_Cond_03.png" width="280"/>
  <img src="https://github.com/NathanaelCruz/images_resource_projects/blob/master/Images/screen_Cond_04.png" width="280"/>
  
</section>

Layout simples e limpo para utilizar da melhor forma.

![GitHub All Releases](https://img.shields.io/github/downloads/NathanaelCruz/condominium/total)
![GitHub top language](https://img.shields.io/github/languages/top/NathanaelCruz/condominium)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/NathanaelCruz/condominium)
![GitHub issues](https://img.shields.io/github/issues/NathanaelCruz/condominium)
![GitHub last commit](https://img.shields.io/github/last-commit/NathanaelCruz/condominium)
![GitHub](https://img.shields.io/github/license/NathanaelCruz/condominium?style=plastic)

### Sobre o Projeto
> A idéia é realizar o gerenciamento de entradas e saídas de um condomínio, podendo realizar o registro e atualização de visitantes, moradores e de funcionários.


### Motivação
Este  um projeto pessoal para portfólio, então feedbacks são bem-vindos, seja de estrutura do projeto ou mesmo codificação, pois acredito que sempre podemos melhorar em algo.

Conecte-se comigo no linkedin [aqui](https://www.linkedin.com/in/nathanael-cruz-alves/)


### Requerimentos para Utilizar
1. Servidor Web com suporte a PHP >= 7.1 (recomendação APACHE)
1. Servidor com suporte a Twig Framework
1. MySql >= 5
1. PDO habilitado
1. Permissão para upload de arquivos
1. .htaccess permitido no servidor


### Pré-Configuração
Antes de começar a utilizar, siga os passos abaixo:
1. Importe o arquivo RESIDENTIAL_CONDOMINIUM_DB.sql no mysql.
1. Antes de entrar no site, abra o arquivo no caminho abaixo:
    - App/Config/Config.php
      * Nele será possvel configurar as váriaveis padrões de ambiente, como:
      * Host name;
      * Database name;
      * Login e Paswword do database;
      * Configuraçes de e-mail, como endereço de e-mail, nome de envio, porta e demais.
1. Após realizar a configuração e importação do banco de dados, poderá utilizar o sistema.

## Login Administrador
Login: admin  
Senha: admin  
Permissão: Master  


### Funcionalidades & Utilizações
O sistema apresenta um layout responsivo e com designer flat, adaptável para aparelhos móveis, além de possuir o cadastro de usuários através da Webcam. O cadastro de entradas e saídas, afim de agilizar os registros, é realizado através da busca de CPF ou RG para validação imediata, apresentando dados de quem será cadastrado, como nome completo e foto de cadastro.
Também há a possibilidade de extração de relatórios para acompanhamento do desempenho do condomínio, possuindo faixas de horário onde há visitantes, total de moradores e visitantes, registros de Saídas e mais informações pertinentes para o acompanhamento comercial do local.

#### Desenvolvido em 2019
