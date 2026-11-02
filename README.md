# CicloTech

Sistema web acadêmico para gerenciamento de equipamentos eletrônicos, clientes, funcionários e serviços de manutenção, com foco em uma proposta de economia circular.

## Funcionalidades

- Cadastro e consulta de clientes e funcionários.
- Cadastro, consulta e edição de equipamentos.
- Controle de situação e ativação/inativação de registros.
- Registro e acompanhamento de manutenções.
- Sincronização da situação do equipamento com o andamento da manutenção.
- Painel administrativo com indicação de equipamentos doados aguardando abertura de manutenção.

## Tecnologias utilizadas

- PHP
- HTML e CSS
- Bootstrap 5.3.3
- JavaScript
- Arquivos JSON para armazenamento dos dados

## Como executar

1. Instale o [XAMPP](https://www.apachefriends.org/).
2. Copie a pasta `ciclotech` para `htdocs`.
3. Inicie o Apache pelo painel do XAMPP.
4. Dentro da pasta includes, copie o arquivo
   config.example.php e renomeie a cópia para config.php.
5. Abra config.php e defina suas próprias credenciais administrativas.
6. Acesse http://localhost/ciclotech/ pelo navegador.

> O caminho da URL pode variar conforme a pasta em que o projeto for instalado dentro de `htdocs`.

## Estrutura principal

```text
ciclotech/
├── admin/       # Telas e funcionalidades administrativas
├── assets/css/  # Estilos do sistema
├── dados/       # Arquivos JSON utilizados pelo sistema
├── includes/    # Configurações e funções compartilhadas
└── index.php    # Página inicial
```

## Observações

- Os dados são armazenados localmente em arquivos JSON; o projeto não utiliza banco de dados MySQL.
- Antes de publicar o projeto, não envie credenciais reais nem dados pessoais. Mantenha `includes/config.php` fora do repositório e disponibilize apenas um arquivo de exemplo de configuração.
- Projeto desenvolvido para fins acadêmicos.
