#  Sistema de Controle de Estacionamento Inteligente

Sistema completo de gerenciamento de estacionamento desenvolvido em PHP 8+ aplicando princípios SOLID, DRY, KISS e Object Calisthenics.


##  Visão Geral

Sistema de controle de estacionamento que permite:
-  Cadastrar entradas e saídas de veículos
-  Calcular tarifas baseadas em tempo e tipo de veículo
-  Gerar relatórios de uso e faturamento
-  Aplicar os princípios SOLID e boas práticas de engenharia de software

##  Tecnologias Utilizadas

- *PHP 8.2+*: Linguagem principal com typed properties e enums
- *SQLite*: Banco de dados leve e eficiente
- *Composer*: Gerenciador de dependências com autoload PSR-4
- *Tailwind CSS*: Framework CSS para interface moderna
- *SweetAlert2*: Biblioteca para alerts elegantes
- *PSR-12*: Padrão de código seguido

##  Arquitetura e Estrutura


estacionamento_P2/
├── src/
│   ├── Application/
│   │   └── Services/
│   │       ├── RegistrarEntradaService.php
│   │       ├── RegistrarSaidaService.php
│   │       └── GerarRelatorioService.php
│   ├── Domain/
│   │   ├── Interfaces/
│   │   │   ├── interfaceCalcTarifa.php
│   │   │   └── registroEstacionamentoRepositoryInterface.php
│   │   ├── registrarEstacionamento.php
│   │   ├── TipoVeiculo.php (Enum)
│   │   ├── tarifaCarro.php
│   │   ├── tarifaMotocicleta.php
│   │   ├── tarifaCaminhao.php
│   │   └── tarifaFactory.php
│   └── Infra/
│       ├── Repositories/
│       │   └── SqliteEstadiaRepository.php
│       └── SqliteConnection.php
├── public/
│   └── index.php
├── setup_database.php
├── composer.lock
└── composer.json


### Camadas da Aplicação

####  *Application Layer*
- *Services*: Orquestram casos de uso específicos
  - RegistrarEntradaService: Valida e registra entrada de veículos
  - RegistrarSaidaService: Calcula tarifa e registra saída
  - GerarRelatorioService: Gera estatísticas e relatórios

####  *Domain Layer*
- *Entities*: Regras de negócio puras
  - EstadiaEstacionamento: Entidade principal com lógica de permanência
  - TipoVeiculo: Enum com tipos válidos de veículos
- *Value Objects & Strategy Pattern*
  - TarifaCarro, TarifaMotocicleta, TarifaCaminhao: Estratégias de cálculo
  - TarifaFactory: Factory para criar calculadoras de tarifa
- *Interfaces*: Contratos para inversão de dependência

####  *Infrastructure Layer*
- *Repositories*: Acesso a dados
- *Database*: Conexão e persistência SQLite

##  Princípios Aplicados

### SOLID

#### 1 *Single Responsibility Principle (SRP)*
Cada classe tem uma única responsabilidade:
- EstadiaEstacionamento: Gerencia dados de uma estadia
- RegistrarEntradaService: Apenas registra entradas
- SqliteEstadiaRepository: Apenas persiste dados

#### 2 *Open/Closed Principle (OCP)*
Sistema aberto para extensão, fechado para modificação:
php
// Novo tipo de veículo adicionado sem modificar código existente
enum TipoVeiculo: string {
    case Carro = 'carro';
    case Motocicleta = 'motocicleta';
    case Caminhao = 'caminhao';
    // case NovoTipo = 'novo_tipo'; ← Apenas adicionar
}


#### 3 *Liskov Substitution Principle (LSP)*
Todas as tarifas implementam a mesma interface:
php
interface CalculoTarifa {
    public function calcularTarifa(int $tempo): float;
}
// TarifaCarro, TarifaMotocicleta e TarifaCaminhao são substituíveis


#### 4 *Interface Segregation Principle (ISP)*
Interfaces específicas e coesas:
- CalculoTarifa: Apenas para cálculo de tarifas
- RegistroEstacionamentoRepositoryInterface: Apenas para persistência

#### 5 *Dependency Inversion Principle (DIP)*
Services dependem de abstrações:
php
public function __construct(
    private RegistroEstacionamentoRepositoryInterface $repository
) {}


### Outros Princípios

####  *DRY (Don't Repeat Yourself)*
- Factory Pattern evita repetição na criação de calculadoras
- Repository Pattern centraliza acesso aos dados

####  *KISS (Keep It Simple, Stupid)*
- Lógica de negócio clara e direta
- Métodos pequenos e focados

####  *Object Calisthenics*
-  Apenas um nível de indentação por método
-  Não use ELSE
-  Encapsule coleções
-  Use getters de forma consciente
-  Mantenha entidades pequenas

##  Instalação e Execução

### Pré-requisitos
- PHP 8.2 ou superior
- Composer instalado
- Extensão SQLite habilitada

### Passo a Passo

1. *Clone o repositório*
bash
git clone https://github.com/seu-usuario/estacionamento_P2.git
cd estacionamento_P2


2. *Instale as dependências*
bash
composer install


3. *Configure o banco de dados*
bash
php setup_database.php


4. *Inicie o servidor PHP*
bash
cd public
php -S localhost:8000


5. *Acesse no navegador*

http://localhost:8000


##  Regras de Negócio

### Tipos de Veículos e Tarifas

| Tipo | Tarifa por Hora |
|------|----------------|
|  Carro | R$ 5,00 |
|  Moto | R$ 3,00 |
|  Caminhão | R$ 10,00 |

### Cálculo de Permanência
- Tempo calculado em *horas completas*
- Arredondamento *sempre para cima*
- Exemplos:
  - 30 minutos = 1 hora
  - 1h 15min = 2 horas
  - 2h 50min = 3 horas

### Validações
-  Placa no formato brasileiro: ABC1234 ou ABC1D23
-  Não permite entrada duplicada (placa já ativa)
-  Saída apenas para veículos com entrada registrada
-  Tipo de veículo deve ser válido

##  Funcionalidades

### 1. Registrar Entrada
- Valida formato da placa
- Verifica se veículo já está no estacionamento
- Registra data/hora de entrada automaticamente

### 2. Registrar Saída
- Calcula tempo de permanência
- Aplica tarifa correta baseada no tipo de veículo
- Gera valor total a pagar
- Finaliza estadia

### 3. Relatório Geral
- Total de veículos (ativos + finalizados)
- Quantidade de veículos ativos
- Quantidade de veículos que já saíram
- Faturamento total
- Detalhamento por tipo de veículo

##  Exemplos de Uso

### Entrada de Veículo

Placa: ABC1234
Tipo: Carro
Resultado: Entrada registrada às 10:00


### Saída de Veículo

Placa: ABC1234
Entrada: 10:00
Saída: 12:30
Tempo: 3 horas (arredondado)
Valor: R$ 15,00 (3h × R$ 5,00)


##  Interface

A interface foi desenvolvida com:
- *Tailwind CSS*: Design moderno e responsivo
- *SweetAlert2*: Feedback visual elegante
- *HTML Semântico*: Acessibilidade e SEO

##  Testes e Validação

Para testar o sistema:

1. Registre algumas entradas de diferentes tipos
2. Registre saídas para calcular tarifas
3. Verifique o relatório para conferir totalizações
4. Teste validações (placa inválida, entrada duplicada, etc.)

##  Decisões Técnicas

### Por que SQLite?
- Sem necessidade de servidor de banco separado
- Arquivo único e portável
- Ideal para projetos de médio porte

### Por que Strategy Pattern para Tarifas?
- Facilita adição de novos tipos de veículos
- Isola lógica de cálculo
- Segue OCP do SOLID

### Por que Services na Application Layer?
- Orquestração de casos de uso
- Desacopla domínio da infraestrutura
- Facilita testes unitários

## 👥 Integrantes do Grupo

- Cristhian Heber - 2019595
- Miguel Pires    - 1999181


## 📄 Licença

Este projeto foi desenvolvido para fins acadêmicos.

---