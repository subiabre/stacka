# stacka
`stacka` is a self-hosted, personal-use oriented, [perpetual inventory manager](https://www.investopedia.com/terms/p/perpetualinventory.asp) for CLI systems.

You can use stacka to record transactions on assets of any kind and obtain updated data about amounts, average costs, sales and earnings.

1. [Installation](#installation)
    1. [Prerequisites](#prerequisites)
    2. [Install](#install)
2. [Usage](#usage)

# Installation

## Prerequisites
This application is shipped in a dockerized fashion to minimize the dependencies you need to install on your own. In order to make it work your system needs to have:

- [docker](https://docs.docker.com/engine/install/)
- [docker-compose](https://docs.docker.com/compose/install/)

Aditionally, you can use [make](https://www.gnu.org/software/make/) to ease some processes. This is a tool that you'd usually find already installed in most GNU systems.

## Install
Clone or download this repository:
```shell
git clone https://github.com/subiabre/stacka
cd stacka
```

### Using `make`
With `make`, in the directory for stacka:
```shell
make install
```

This will allow you to run commands directly on the docker container from anywhere by just typing `stacka <command>`. Further examples on this document assume you've completed this step. If not, please note that you need to execute the commands inside the docker instance for stacka. Continue reading to complete the installation process without `make`. Otherwise you can skip to the next section.

### Using `bin/docker`

Because stacka is shipped as a dockerized app you need to build the docker instance, install the dependencies and initialize the database manually. To ease this you can use the `bin/docker` shell script that includes useful shortcuts.

```shell
# Builds the docker images, installs the composer dependencies and initializes the database
bin/docker install

# To rebuild the containers
bin/docker build
# To start the containers
bin/docker up
# To stop the containers
bin/docker down
# To start a bash session on the php container
bin/docker php
# To start a bash session on the mariadb container
bin/docker mariadb
# To relay commands to stacka
bin/docker <command>
```

Note that during installation via `make` a modified version of this script is set as `stacka` on your system.

# Usage
stacka is for managing abstract Assets using Transactions to modify the accountancy data.

## Basic usage
Get a list of available commands:
```bash
stacka list
```

If you need help with a command:
```bash
stacka help <command>

# Same as
stacka <command> --help
```

## How to work with `stacka`
Let's start with an example. Say you have a shop where you sell furniture: chairs, tables, etc.

### Adding Assets
```bash
stacka add chairs
stacka add tables
stacka add stools
```

### Reading Assets
You just created three Assets; these are individual Transaction holders for items of the same type, transacted under the same currency and with a common accounting, between other properties. See them with:
```bash
# See general data for `chairs`, `tables` and `stools`
stacka assets
# See Transactions data for `chairs`
stacka read chairs
# See details for `chairs`
stacka info chairs
```

You can list Assets based on their name by using the `%` wildcard. Combining this character with the name argument will let you see Assets with similar names.
```bash
# Will show Assets that start with `chair`, e.g: "chairs_garden", "chairs_office"
stacka assets chair%
# Will show Assets that end with `les`, e.g: "tables", "cables"
stacka assets %les
# Will show Assets that contain `oo`, eg: "stools", "doors"
stacka assets %oo%
```

### Recording buys and sales
Each time you buy or sell one of the items in your shop that's a Transaction.
```bash
# Bought chairs, 10 units at a total value of 90
stacka new buy chairs 10 90
# Sold chairs, 1 unit at a total value of 12
stacka new sale chairs 1 12
```

Keep in mind that each Asset represents an homogenous item. For two different models of chairs that you buy and sell with different currencies, or with different accounting methods, you will need to register Transactions separately.
```bash
stacka add chairs_office
stacka add chairs_garden
```

### Using different accounting models
Now let's say you want to know your earnings on chairs by [FIFO](https://www.investopedia.com/terms/f/fifo.asp) but for your tables on [LIFO](https://www.investopedia.com/terms/l/lifo.asp). The default accounting method is actually average, so you need to update these Assets, for that you'll need to supply a valid accounting key. You can get a list of all available accounting models for your books with the `stacka accountings` command.

```bash
# Update `chairs` with `fifo` and `tables` with `lifo` accounting
stacka edit chairs --asset.accounting=fifo
stacka edit tables --asset.accounting=lifo
```
There are many more options to supply as Asset properties.
```bash
# New Asset `desks` will use `fifo` accounting and take `EUR` as currency
stacka add desks --asset.accounting=fifo --asset.moneyCurrency=EUR
```
To get a complete list of options pass the `--help` flag to any of the previous commands.
