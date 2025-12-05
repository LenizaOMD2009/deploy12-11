-- Initial migration: create core tables and view (idempotent)

-- Table: usuario
CREATE TABLE IF NOT EXISTS uf(
    id BIGSERIAL PRIMARY KEY,
    sigla TEXT,
    nome TEXT,
    data_cadastro TIMESTAMP DEFAULT current_timestamp,
    data_alteracao TIMESTAMP DEFAULT current_timestamp
);

CREATE TABLE IF NOT EXISTS cidade(
    id BIGSERIAL PRIMARY KEY,
    id_uf BIGINT,
    nome TEXT,
    ibge TEXT,
    data_cadastro TIMESTAMP DEFAULT current_timestamp,
    data_alteracao TIMESTAMP DEFAULT current_timestamp,
    CONSTRAINT cidade_id_uf FOREIGN KEY (id_uf) REFERENCES uf(id)
);

CREATE TABLE IF NOT EXISTS cliente(
    id BIGSERIAL PRIMARY KEY,
    nome_fantasia TEXT,
    sobrenome_razao TEXT,
    cpf_cnpj TEXT,
    rg_ie TEXT,
    data_nascimento_abertura DATE,
    data_cadastro TIMESTAMP DEFAULT current_timestamp,
    data_alteracao TIMESTAMP DEFAULT current_timestamp
);

CREATE TABLE IF NOT EXISTS usuario (
    id BIGSERIAL PRIMARY KEY,
    nome TEXT,
    sobrenome TEXT,
    cpf TEXT,
    rg TEXT,
    senha TEXT,
    codigo_recuperacao TEXT,
    ativo BOOLEAN DEFAULT FALSE,
    administrador BOOLEAN DEFAULT FALSE,
    data_cadastro TIMESTAMP DEFAULT current_timestamp,
    data_alteracao TIMESTAMP DEFAULT current_timestamp
);

CREATE TABLE IF NOT EXISTS empresas(
    id BIGSERIAL PRIMARY KEY,
    nome_fantasia TEXT,
    sobrenome_razao TEXT,
    cpf_cnpj TEXT,
    rg_ie TEXT,
    data_nascimento_abertura DATE,
    data_cadastro TIMESTAMP DEFAULT current_timestamp,
    data_alteracao TIMESTAMP DEFAULT current_timestamp
);

CREATE TABLE IF NOT EXISTS fornecedor(
    id BIGSERIAL PRIMARY KEY,
    nome_fantasia TEXT,
    sobrenome_razao TEXT,
    cpf_cnpj TEXT,
    rg_ie TEXT,
    data_nascimento_abertura DATE,
    data_cadastro TIMESTAMP DEFAULT current_timestamp,
    data_alteracao TIMESTAMP DEFAULT current_timestamp
);

CREATE TABLE IF NOT EXISTS endereco(
    id BIGSERIAL PRIMARY KEY,
    id_cidade BIGINT,
    id_cliente BIGINT,
    id_usuario BIGINT,
    id_empresa BIGINT,
    id_fornecedor BIGINT,
    nome TEXT,
    cep TEXT,
    numero TEXT,
    logradouro TEXT,
    bairro TEXT,
    complemento TEXT,
    referencia TEXT,
    data_cadastro TIMESTAMP DEFAULT current_timestamp,
    data_alteracao TIMESTAMP DEFAULT current_timestamp,
    CONSTRAINT endereco_id_cidade FOREIGN KEY (id_cidade) REFERENCES cidade(id),
    CONSTRAINT contato_id_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id),
    CONSTRAINT contato_id_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id),
    CONSTRAINT contato_id_empresa FOREIGN KEY (id_empresa) REFERENCES empresas(id),
    CONSTRAINT contato_id_fornecedor FOREIGN KEY (id_fornecedor) REFERENCES fornecedor(id)
);

CREATE TABLE IF NOT EXISTS contato(
    id BIGSERIAL PRIMARY KEY,
    id_cliente BIGINT,
    id_usuario BIGINT,
    id_empresas BIGINT,
    id_fornecedor BIGINT,
    tipo TEXT,
    contato TEXT,
    endereco_contato TEXT,
    data_cadastro TIMESTAMP DEFAULT current_timestamp,
    data_alteracao TIMESTAMP DEFAULT current_timestamp,
    CONSTRAINT contato_id_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id),
    CONSTRAINT contato_id_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id),
    CONSTRAINT contato_id_empresas FOREIGN KEY (id_empresas) REFERENCES empresas(id),
    CONSTRAINT contato_id_fornecedor FOREIGN KEY (id_fornecedor) REFERENCES fornecedor(id)
);


CREATE OR REPLACE VIEW vw_usuario_contatos AS
SELECT
    u.id,
    u.nome,
    u.sobrenome,
    u.cpf,
    u.rg,
    u.ativo,
    u.administrador,
    u.senha,
    u.codigo_recuperacao,
    MAX(CASE WHEN c.tipo = 'email' THEN c.contato END) AS email,
    MAX(CASE WHEN c.tipo = 'celular' THEN c.contato END) AS celular,
    MAX(CASE WHEN c.tipo = 'whatsapp' THEN c.contato END) AS whatsapp,
    u.data_cadastro,
    u.data_alteracao
FROM usuario u
LEFT JOIN contato c ON c.id_usuario = u.id
GROUP BY u.id, u.nome, u.sobrenome, u.cpf, u.rg, u.ativo, u.administrador, u.senha, u.codigo_recuperacao, u.data_cadastro, u.data_alteracao;
