# Finance Tracker — API REST de Gerenciamento Financeiro

Este projeto é uma **API RESTful** desenvolvida em **Laravel 12**, com foco no gerenciamento de transações financeiras pessoais. A aplicação permite o controle de entradas e saídas, categorização inteligente, relatórios e autenticação segura via **Laravel Sanctum**.

Todo o código foi construído com base nos princípios **SOLID**, **Clean Code** e nas recomendações da [PSR](https://www.php-fig.org/psr/) para manter a escalabilidade e legibilidade do sistema.

---

## Funcionalidades

- ✅ Cadastro e login de usuários via API (Sanctum)
- ✅ Criação, edição e remoção de transações (entrada/saída)
- ✅ Categorização automática com base na descrição da transação
- ✅ Gerenciamento de categorias personalizadas e padrão
- ✅ Relatórios mensais e personalizados por período
- ✅ Estrutura de testes automatizados com banco em memória
- ✅ Validação avançada via Form Requests
- ✅ API 100% JSON, pronta para front-end ou app mobile

---

## Tecnologias Utilizadas

- PHP 8.2+
- Laravel 12.x
- Laravel Sanctum
- PHPUnit
- SQLite (para testes)
- MySQL (opcional)
- Insmnia (para testes)

---

## TESTES

- Todas as rotas do sistema contam com testes feature automatizados
- Teste com `php artisan test`

---

## Categorização Inteligente

Ao registrar uma transação, o sistema tenta automaticamente atribuir uma categoria com base nas palavras-chave da descrição.
Exemplos:

"Pix Salário" → Financeiro

"Uber 10h" → Transporte

"ida no mc'donals" -> Alimentação

Isso facilita o uso e reduz esforço manual do usuário.

---

## Instalação

> Requisitos: PHP, Composer, SQLite/MySQL

```bash
# 1. Clonar o repositório
git clone https://github.com/seu-usuario/finance-tracker.git
cd finance-tracker

# 2. Instalar dependências
composer install

# 3. Copiar variáveis de ambiente
cp .env.example .env
php artisan key:generate

# 4. Configurar banco (SQLite recomendado no começo)
php artisan migrate

# 5. Iniciar o servidor local
php artisan serve
```

## LIÇENCA

Projeto feito por Henrique Texeira, em caso de qualquer problema com o projeto, me mande um email: henriquedepaula1015@gmail.com
