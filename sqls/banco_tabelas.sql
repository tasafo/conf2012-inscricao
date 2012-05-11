CREATE  TABLE IF NOT EXISTS tipo_inscricao (
  id INT NOT NULL AUTO_INCREMENT ,
  descricao VARCHAR(45) NOT NULL ,
  valor DOUBLE NOT NULL ,
  status CHAR(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (id) ,
  UNIQUE INDEX descricao_UNIQUE (descricao ASC) )
ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT = 1;

CREATE  TABLE IF NOT EXISTS inscricao (
  id INT NOT NULL AUTO_INCREMENT ,
  data_registro DATETIME NOT NULL ,
  data_pagamento DATETIME NULL ,
  data_compensacao DATETIME NULL ,
  tipo_pagamento VARCHAR(50) NULL ,
  status_transacao VARCHAR(50) NULL ,
  transacao_id VARCHAR(100) NULL ,
  taxa DOUBLE NULL DEFAULT 0 ,
  id_tipo_inscricao INT NOT NULL ,
  id_empresa INT NULL DEFAULT 0,
  PRIMARY KEY (id) ,
  INDEX fk_inscricao_tipo_inscricao1 (id_tipo_inscricao ASC) ,
  CONSTRAINT fk_inscricao_tipo_inscricao1
    FOREIGN KEY (id_tipo_inscricao)
    REFERENCES tipo_inscricao (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT = 1;

CREATE  TABLE IF NOT EXISTS empresa (
  id INT NOT NULL AUTO_INCREMENT ,
  nome VARCHAR(100) NOT NULL ,
  responsavel VARCHAR(50) NOT NULL ,
  email VARCHAR(45) NOT NULL ,
  cep CHAR(8) NOT NULL , 
  PRIMARY KEY (id) )
ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT = 1;

CREATE  TABLE IF NOT EXISTS individual (
  id INT NOT NULL AUTO_INCREMENT ,
  nome VARCHAR(60) NOT NULL ,
  email VARCHAR(50) NOT NULL ,
  profissao VARCHAR(50) NOT NULL ,
  instituicao VARCHAR(100) NOT NULL ,
  cep CHAR(8) NOT NULL ,
  situacao CHAR(1) NOT NULL DEFAULT 'A',
  presente CHAR(1) NOT NULL DEFAULT 'N',
  quem_registrou_presenca VARCHAR(50) NULL ,
  permito_divulgacao CHAR(1) NOT NULL DEFAULT 'N',
  id_inscricao INT NOT NULL ,
  PRIMARY KEY (id) ,
  INDEX fk_individual_inscricao1 (id_inscricao ASC) ,
  CONSTRAINT fk_individual_inscricao1
    FOREIGN KEY (id_inscricao)
    REFERENCES inscricao (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS usuario (
  id int(11) NOT NULL AUTO_INCREMENT,
  nome varchar(50) NOT NULL,
  email varchar(50) NOT NULL,
  senha varchar(50) NOT NULL,
  perfis varchar(100) NOT NULL,
  tema_palestra varchar(100) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1;