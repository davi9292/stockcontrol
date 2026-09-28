-- StockControl — banco de dados e dados de demonstração
-- Compatível com MySQL 8+ e MariaDB 10.4+.
-- Contas: admin@stockcontrol.local / password
--         usuario@stockcontrol.local / password

CREATE DATABASE IF NOT EXISTS stockcontrol
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE stockcontrol;

CREATE TABLE IF NOT EXISTS usuarios (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  senha_hash VARCHAR(255) NOT NULL,
  perfil ENUM('administrador', 'usuario') NOT NULL DEFAULT 'usuario',
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_email (email),
  KEY idx_usuarios_perfil_ativo (perfil, ativo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produtos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(160) NOT NULL,
  descricao TEXT NOT NULL,
  quantidade INT UNSIGNED NOT NULL DEFAULT 0,
  preco DECIMAL(10,2) UNSIGNED NOT NULL,
  status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
  imagem VARCHAR(255) NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  excluido_em DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_produtos_status_nome (status, nome),
  KEY idx_produtos_excluido_em (excluido_em)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS movimentacoes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  produto_id BIGINT UNSIGNED NOT NULL,
  usuario_id BIGINT UNSIGNED NOT NULL,
  tipo ENUM('entrada', 'saida') NOT NULL,
  quantidade INT UNSIGNED NOT NULL,
  data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  observacao TEXT NULL,
  PRIMARY KEY (id),
  KEY idx_movimentacoes_produto_data (produto_id, data_hora),
  KEY idx_movimentacoes_usuario_data (usuario_id, data_hora),
  CONSTRAINT fk_movimentacoes_produto FOREIGN KEY (produto_id)
    REFERENCES produtos (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_movimentacoes_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios (id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS comentarios (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  produto_id BIGINT UNSIGNED NOT NULL,
  usuario_id BIGINT UNSIGNED NOT NULL,
  texto TEXT NOT NULL,
  data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_comentarios_produto_data (produto_id, data_hora),
  KEY idx_comentarios_usuario_data (usuario_id, data_hora),
  CONSTRAINT fk_comentarios_produto FOREIGN KEY (produto_id)
    REFERENCES produtos (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_comentarios_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios (id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Este bcrypt é válido para password_verify() e corresponde à senha "password".
INSERT INTO usuarios (id, nome, email, senha_hash, perfil, ativo) VALUES
(1, 'Ana Administradora', 'admin@stockcontrol.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1),
(2, 'Bruno Usuário', 'usuario@stockcontrol.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario', 1);

INSERT INTO produtos (id, nome, descricao, quantidade, preco, status, imagem, criado_em) VALUES
(1, 'Teclado USB', 'Teclado ABNT2 com conexão USB.', 18, 89.90, 'ativo', NULL, '2026-09-01 09:00:00'),
(2, 'Mouse Óptico', 'Mouse óptico USB com três botões.', 32, 49.90, 'ativo', NULL, '2026-09-02 09:00:00'),
(3, 'Monitor 24 polegadas', 'Monitor LED Full HD para uso administrativo.', 7, 899.00, 'ativo', NULL, '2026-09-03 09:00:00'),
(4, 'Cabo HDMI', 'Cabo HDMI de 2 metros.', 25, 29.90, 'ativo', NULL, '2026-09-04 09:00:00'),
(5, 'Headset', 'Headset com microfone integrado.', 12, 139.90, 'ativo', NULL, '2026-09-05 09:00:00'),
(6, 'Webcam HD', 'Webcam USB com resolução HD.', 9, 179.00, 'ativo', NULL, '2026-09-06 09:00:00'),
(7, 'Hub USB', 'Hub USB com quatro portas.', 15, 59.90, 'ativo', NULL, '2026-09-07 09:00:00'),
(8, 'Suporte para notebook', 'Suporte ajustável para notebook.', 0, 99.90, 'inativo', NULL, '2026-09-08 09:00:00');

INSERT INTO movimentacoes (id, produto_id, usuario_id, tipo, quantidade, data_hora, observacao) VALUES
(1, 1, 1, 'entrada', 20, '2026-09-10 08:30:00', 'Saldo inicial do lote.'),
(2, 1, 2, 'saida', 2, '2026-09-10 10:00:00', 'Separado para o setor administrativo.'),
(3, 2, 1, 'entrada', 35, '2026-09-11 08:30:00', 'Reposição de estoque.'),
(4, 2, 2, 'saida', 3, '2026-09-11 11:00:00', 'Entrega para novos postos.'),
(5, 3, 1, 'entrada', 8, '2026-09-12 09:00:00', 'Recebimento de monitores.'),
(6, 3, 2, 'saida', 1, '2026-09-12 14:00:00', 'Instalado na sala de reunião.'),
(7, 4, 1, 'entrada', 25, '2026-09-13 09:00:00', 'Compra mensal.'),
(8, 5, 1, 'entrada', 12, '2026-09-14 09:00:00', 'Lote recebido.'),
(9, 6, 2, 'entrada', 10, '2026-09-15 10:00:00', 'Lote de dez unidades recebido.'),
(10, 6, 2, 'saida', 1, '2026-09-15 15:00:00', 'Alocada para atendimento remoto.'),
(11, 7, 1, 'entrada', 15, '2026-09-16 08:30:00', 'Saldo inicial do lote de hubs USB.');

INSERT INTO comentarios (id, produto_id, usuario_id, texto, data_hora) VALUES
(1, 1, 1, 'Produto conferido no recebimento.', '2026-09-10 08:40:00'),
(2, 1, 2, 'Teclas funcionando corretamente.', '2026-09-10 10:10:00'),
(3, 2, 1, 'Modelo padrão para os novos postos.', '2026-09-11 08:45:00'),
(4, 2, 2, 'Um item foi entregue ao suporte.', '2026-09-11 11:10:00'),
(5, 3, 1, 'Caixas recebidas sem avarias.', '2026-09-12 09:10:00'),
(6, 3, 2, 'Instalação concluída na sala de reunião.', '2026-09-12 14:10:00'),
(7, 4, 1, 'Cabo compatível com os monitores do setor.', '2026-09-13 09:10:00'),
(8, 5, 2, 'Microfone testado antes da entrega.', '2026-09-14 10:00:00'),
(9, 6, 1, 'Lote recebido e revisado.', '2026-09-15 10:10:00'),
(10, 8, 1, 'Item inativo após encerramento do modelo.', '2026-09-16 09:00:00');
