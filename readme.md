*Sistema de Controle de Estacionamento*

Projeto acadêmico desenvolvido em PHP 8+, aplicando SOLID, DRY, KISS, Object Calisthenics, padrões PSR-12 e
arquitetura em camadas Domain / Application / Infra.
Este sistema simula a gestão completa de um estacionamento, incluindo registros, cálculo de tarifas e relatórios.


*Objetivo do Projeto*
Criar um sistema simples, modular e escalável que permita:

-Registrar entrada de veículos

-Registrar saída e cálculo automático de tarifa

-Controlar valores por tipo de veículo (Carro, Moto, Caminhão)

-Persistir dados em SQLite

-Gerar relatórios completos de uso e faturamento

-Utilizar boas práticas de engenharia de software

  
*Regras de Negócio*
Tipos de veículo e tarifas:
Tipo	Tarifa (por hora)
Carro	R$ 5/h
Moto	R$ 3/h
Caminhão	R$ 10/h
Regras adicionais:

Tempo de permanência arredondado para cima
Ex.: 1h05 → cobra 2 horas

- Registro obrigatório de entrada e saída

Relatório deve exibir:

- Quantidade de veículos por tipo

- Tempo total

- Faturamento por categoria

- Faturamento geral


 *Arquitetura e Estrutura do Projeto*

O projeto foi desenvolvido utilizando uma arquitetura limpa e modular, dividida em camadas bem definidas.

src/
 ├─ Application/
 │   └─ services/
 │       ├─ GerarRelatorioService.php
 │       ├─ RegistrarEntradaService.php
 │       └─ RegistrarSaidaService.php
 │
 ├─ Domain/
 │   ├─ Interfaces/
 │   │   ├─ interfaceCalcTarifa.php
 │   │   └─ RegistroEstacionamentoRepositoryInterface.php
 │   │
 │   ├─ EstadiaEstacionamento.php
 │   ├─ registrarEstacionamento.php
 │   ├─ tarifaCaminhao.php
 │   ├─ tarifaCarro.php
 │   ├─ tarifaFactory.php
 │   ├─ tarifaMotocicleta.php
 │   └─ TipoVeiculo.php
 │
 └─ Infra/
     ├─ database/
     │   └─ estacionamento.sqlite
     │
     └─ Repositories/
         ├─ SqliteEstadiaRepository.php
         └─ SqliteConnection.php

 *Descrição das Camadas*
 
*Application* 
Aqui ficam os serviços responsáveis por orquestrar ações do sistema:

services/
RegistrarEntradaService.php
Registra a entrada de um veículo.

RegistrarSaidaService.php
Calcula o valor, registra saída e finaliza a estadia.

GerarRelatorioService.php
Faz o cálculo total do faturamento e quantidade por tipo de veículo.

//Aplica DIP: é a injeção de dependência via repositório//


 *Domain* 
É a camada que contém toda a lógica principal do estacionamento.

Interfaces/
interfaceCalcTarifa.php
Interface para estratégias de tarifa (carro, moto, caminhão).

RegistroEstacionamentoRepositoryInterface.php
Contrato que define como o repositório deve se comportar.

Classes de Entidade / Lógica:
EstadiaEstacionamento.php
Entidade que representa a estadia completa do veículo.

registrarEstacionamento.php
Entidade utilizada para registrar entrada.

TipoVeiculo.php
Enum/constante indicando os tipos: carro, moto, caminhão.

Tarifas (estratégia de cálculo):
tarifaCarro.php

tarifaMotocicleta.php

tarifaCaminhao.php

Factory:
tarifaFactory.php
Retorna a estratégia correta com base no tipo de veículo.

 //Aplica OCP: adicionar novo tipo sem alterar código existente//

 *Infra (Infraestrutura / Banco / Implementações)
Aqui ficam os detalhes concretos do SQLite.

database/
estacionamento.sqlite
Banco de dados utilizado pela aplicação.

Repositories/
SqliteEstadiaRepository.php
Implementação do repositório usando SQLite.

SqliteConnection.php
Classe responsável por criar e retornar a conexão PDO.

//Esta camada conhece detalhes do banco, mas o restante do sistema não conhece SQLite, cumprindo DIP perfeitamente//

*Arquivos Externos Importantes*
setup_database.php
Script que cria e configura o banco de dados SQLite.

composer.json
Define autoload PSR-4 e dependências do projeto.

public/
Contém os arquivos acessados pelo navegador (HTML,forms...).


*Instalação e Execução*
1- Instalar dependências
composer install

2- Criar banco de dados SQLite

Execute o script:

php setup_database.php


Esse script cria o arquivo:

database.sqlite

3- Iniciar servidor local
php -S localhost:8000 -t public


*Acesse:*

 http://localhost:8000

 *Relatórios Disponíveis*

-Total de entradas por tipo

-aturamento individual (carro, moto, caminhão)

-Tempo total estacionado

-Valor total recebido

-Quantidade total de veículos.

