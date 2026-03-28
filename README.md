# OrderShield

Plataforma backend para **análise de risco de pedidos**, com processamento assíncrono, auditoria e arquitetura orientada a eventos.

---

## 🚀 Tecnologias

* PHP 8.4 / Laravel 13
* MySQL 8
* Redis (filas e cache)
* Docker / Docker Compose
* Laravel Sanctum (autenticação)

---

## 🧠 Visão Geral

O OrderShield é um sistema responsável por:

* Receber pedidos via API
* Avaliar risco com base em regras
* Classificar automaticamente o pedido
* Registrar auditoria completa
* Permitir análise posterior

---

## ⚙️ Arquitetura

```text
Client → API → Controller → Action → Job → Service → Database
                                  ↓
                               Audit Log
```

### Fluxo principal

1. Pedido é criado via API
2. Pedido inicia como `pending`
3. Job é disparado para análise de risco
4. Serviço calcula score
5. Pedido é atualizado (`approved`, `under_review`, `blocked`)
6. Resultado é salvo em `risk_analyses`
7. Auditoria é registrada

---

## 📦 Estrutura do Projeto

```text
ordershield/
├── docker/
├── src/
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── ...
├── docker-compose.yml
```

---

## 🐳 Como rodar o projeto

### 1. Subir containers

```bash
docker compose up -d --build
```

---

### 2. Instalar dependências

```bash
docker compose exec app composer install
```

---

### 3. Configurar ambiente

```bash
cp src/.env.example src/.env
```

---

### 4. Gerar chave

```bash
docker compose exec app php artisan key:generate
```

---

### 5. Rodar migrations

```bash
docker compose exec app php artisan migrate
```

---

### 6. Subir worker de fila

```bash
docker compose exec app php artisan queue:work
```

---

### 7. Acessar aplicação

```text
http://localhost:8080
```

---

## 🔐 Autenticação

Utiliza **Laravel Sanctum**.

Endpoints protegidos por:

```text
auth:sanctum
```

---

## 📡 Endpoints principais

### Customers

* `POST /api/customers`
* `GET /api/customers`
* `GET /api/customers/{id}`

### Orders

* `POST /api/orders`
* `GET /api/orders`
* `GET /api/orders/{id}`

---

## 📊 Regras de risco (exemplo)

* Pedido acima de R$ 5.000 → +30 pontos
* Cliente recente → +20 pontos
* Múltiplos pedidos em curto período → +20 pontos
* E-mail suspeito → +15 pontos

### Classificação

| Score | Resultado    |
| ----- | ------------ |
| 0–29  | approved     |
| 30–59 | under_review |
| 60+   | blocked      |

---

## 🧾 Auditoria

Todas as ações relevantes são registradas em:

```text
audit_logs
```

Exemplo:

```json
{
  "action": "order.analyzed",
  "entity_type": "order",
  "entity_id": "01HZ...",
  "metadata": {
    "score": 65,
    "classification": "high"
  }
}
```

---

## 🧠 Conceitos aplicados

* Arquitetura em camadas
* Processamento assíncrono (queues)
* Domain-driven thinking (simplificado)
* Enum no PHP para tipagem forte
* ULID como identificador
* Separação entre domínio e entrega (API/Admin)

---

## 📌 Próximos passos

* Dashboard administrativo
* Regras dinâmicas configuráveis
* Integração com serviços externos
* Sistema de alertas em tempo real
* Métricas e observabilidade

---
