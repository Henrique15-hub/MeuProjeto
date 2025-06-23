A ideia é criar um sistema que **simule a vida financeira real de alguém**, tratando entradas e saídas, categorizando tudo, ajudando a entender pra onde o dinheiro vai — tudo isso **via API**, com boas práticas e estrutura profissional.

---

## 🎯 Objetivo Geral

Criar uma **API RESTful** com Laravel que permita o controle financeiro pessoal, com:

* Cadastro de transações (entradas e saídas),
* Classificação por categorias (ex: Alimentação, Transporte),
* Relatórios financeiros (por período, por tipo, por categoria),
* Importação de extratos `.csv` ou `.ofx`,
* Categorização automática baseada em descrições,
* Usuários com contas separadas.

---

## 🧱 Funcionalidades obrigatórias (mínimo viável)

### 🧍 Gestão de usuários

* Registro/Login via Laravel Sanctum. --V
* Cada usuário tem sua própria carteira de transações.

---

### 💰 Transações

* `POST /transacoes`

  * Campos: valor, tipo (`entrada` ou `saida`), descrição, data, categoria.
  * Ex: `R$ 100`, "Supermercado BH", `saida`, categoria `Alimentação`.
* `GET /transacoes`

  * Filtro por data, tipo e categoria. --> rota dedicada
* `PUT /transacoes/{id}`
* `DELETE /transacoes/{id}`

---

### 🗂️ Categorias

* Fixas ou personalizadas:

  * Ex: Alimentação, Transporte, Lazer, Renda Extra.
  * Poderia ter endpoint opcional para o usuário criar as próprias categorias.
* Auto-classificação:

  * Ex: Se descrição contém "Uber", classificar como `Transporte`.
  * Pode ser feito com um map fixo ou com alguma inteligência (dá pra evoluir depois).

---

### 📊 Relatórios

* `GET /relatorios/saldo`

  * Saldo total = entradas - saídas.
* `GET /relatorios/mensal?mes=06&ano=2025`

  * Total por categoria.
  * Percentual de cada categoria.
* `GET /relatorios/grafico`

  * Pode devolver dados em formato pra gráficos (labels + valores).

---

### 📁 Importação de Extratos

* `POST /transacoes/importar`

  * Upload de `.csv` ou `.ofx` com extrato bancário.
  * Processa as linhas e cria transações.
  * Aplica classificação automática.
* Exemplo de `.csv`:

  ```
  15/06/2025;Supermercado BH;-100
  16/06/2025;Pix João;+500
  ```

---

## ✅ Extras que brilham no currículo

### 🔐 Autenticação e segurança

* Sanctum com tokens.
* Middleware para garantir que o usuário só veja os próprios dados.

### 🧪 Testes com PHPUnit

* Testar:

  * Criação de transações.
  * Relatórios.
  * Importação de extrato.
  * Regras de classificação.

### 📂 Organização do código

* Service Layer pra regras de negócio (Ex: `TransactionService`).
* Repositórios, FormRequests, Resources com JSON bonitinho.

---

## 💡 Possíveis extensões depois:

* Planejamento de metas (Ex: gastar no máximo R\$300 em Alimentação no mês).
* Notificações por e-mail (via Laravel Mail) com resumo mensal.
* Exportação dos dados (gerar `.csv` ou `.pdf`).
* Dashboard com Livewire, se quiser brincar com front depois.

---

## 🧠 Tecnologias sugeridas

* Laravel 11
* Sanctum
* PHPUnit
* Banco: SQLite (pra dev), MySQL (produção/Docker)
* Laravel Excel ou League\Csv (pra importação)

---






// Resumo
✅ Lista Completa de Funcionalidades que o Sistema Deve Ter
🧍 Usuários

 - Registro de usuário com e-mail e senha --V
 - Login (com token Sanctum) --V
 - Autenticação para acessar endpoints --V
 - Cada usuário só vê os próprios dados (via middleware) --V

💰 Transações

- Criar transações (entrada ou saída) --V
- Campos: valor, tipo (entrada/saida), descrição, data, categoria --V
- Listar transações  --V
- Filtros: por data, tipo, categoria --V
- Editar transações por ID --V
- Deletar transações por ID --V

🗂️ Categorias

- Categorias padrão (ex: Alimentação, Transporte, etc) --V
- Possibilidade de criar categorias personalizadas --V
- Categorização automática com base na descrição --V

Ex: "Uber" → Transporte

📊 Relatórios

- GET /relatorios/saldo
- Calcula: total de entradas - total de saídas
- GET /relatorios/mensal
- Dados por categoria no mês (soma total e percentual)
- GET /relatorios/grafico
- Dados formatados para gráficos (labels + valores)

📁 Importação de Extratos

- Endpoint para importar .csv ou .ofx
- Faz o upload do arquivo
- Lê as linhas e converte para transações
- Aplica classificação automática

Exemplo de CSV:

    15/06/2025;Supermercado BH;-100
    16/06/2025;Pix João;+500

✅ Extras que valem ponto no currículo
🔐 Segurança

- Sanctum para autenticação com token --V
- Middleware protegendo as rotas privadas --V

🧪 Testes Automatizados (PHPUnit)
 Testar:

- Criação de transações
- Relatórios (mensal/saldo)
- Importação de extrato
- Regras de classificação automática

📂 Organização Profissional do Código

- Service Layer (ex: TransactionService) --V
- Repository Layer (opcional, mas elegante)
- Form Requests para validações --V
- Resources para formatar JSON limpo e padronizado --V

💡 Extensões Futuras (opcional, se quiser ir além)

- Planejamento de metas por categoria
- Envio de e-mail com resumo mensal
- Exportar dados em .csv ou .pdf
- Dashboard com Livewire (se quiser brincar com front)

