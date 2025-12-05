-- Tabela usuario
    CREATE TABLE IF NOT EXISTS usuario (
        id bigserial PRIMARY KEY,
        nome text,
        sobrenome text,
        cpf text,
        rg text,
        data_nascimento date,
        senha text,
        ativo boolean DEFAULT false,
        administrador boolean DEFAULT false,
        codigo_verificacao text,
        data_cadastro timestamp DEFAULT CURRENT_TIMESTAMP,
        data_alteracao timestamp DEFAULT CURRENT_TIMESTAMP
    );
    -- Tabela contato
    CREATE TABLE IF NOT EXISTS contato (
        id bigserial PRIMARY KEY,
        id_usuario bigint,
        tipo text,
        contato text,
        data_cadastro timestamp,
        data_alteracao timestamp,
        CONSTRAINT contato_id_usuario_fkey FOREIGN KEY (id_usuario)
            REFERENCES public.usuario (id)
            ON UPDATE NO ACTION
            ON DELETE NO ACTION
    );
    -- View vw_usuario_contatos
    CREATE OR REPLACE VIEW vw_usuario_contatos AS
    SELECT u.id,
        u.nome,
        u.sobrenome,
        u.cpf,
        u.rg,
        u.senha,
        u.ativo,
        u.administrador,
        u.codigo_verificacao,
        MAX(CASE WHEN c.tipo = 'email' THEN c.contato ELSE NULL END) AS email,
        MAX(CASE WHEN c.tipo = 'celular' THEN c.contato ELSE NULL END) AS celular,
        MAX(CASE WHEN c.tipo = 'whatsapp' THEN c.contato ELSE NULL END) AS whatsapp,
        u.data_cadastro,
        u.data_alteracao
    FROM usuario u
    LEFT JOIN contato c ON c.id_usuario = u.id
    GROUP BY u.id, u.nome, u.sobrenome, u.cpf, u.rg, u.data_cadastro, u.data_alteracao;