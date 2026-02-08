<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260208133515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contesto (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, descrizione VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE direzione (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, descrizione VARCHAR(255) NOT NULL, lingua_partenza_id INTEGER NOT NULL, lingua_arrivo_id INTEGER NOT NULL, CONSTRAINT FK_7B8D3F79C900B759 FOREIGN KEY (lingua_partenza_id) REFERENCES lingua (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7B8D3F796A485162 FOREIGN KEY (lingua_arrivo_id) REFERENCES lingua (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7B8D3F79C900B759 ON direzione (lingua_partenza_id)');
        $this->addSql('CREATE INDEX IDX_7B8D3F796A485162 ON direzione (lingua_arrivo_id)');
        $this->addSql('CREATE TABLE espressione (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, testo VARCHAR(255) NOT NULL, info CLOB DEFAULT NULL, corretta BOOLEAN NOT NULL, lingua_id INTEGER NOT NULL, CONSTRAINT FK_60E5816D762A362F FOREIGN KEY (lingua_id) REFERENCES lingua (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_60E5816D762A362F ON espressione (lingua_id)');
        $this->addSql('CREATE TABLE frase (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, contesto_id INTEGER NOT NULL, direzione_id INTEGER NOT NULL, livello_id INTEGER NOT NULL, espressione_id INTEGER NOT NULL, CONSTRAINT FK_61B903129CC97A21 FOREIGN KEY (contesto_id) REFERENCES contesto (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_61B90312F6B64335 FOREIGN KEY (direzione_id) REFERENCES direzione (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_61B90312B291EE38 FOREIGN KEY (livello_id) REFERENCES livello (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_61B90312D4B622DE FOREIGN KEY (espressione_id) REFERENCES espressione (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_61B903129CC97A21 ON frase (contesto_id)');
        $this->addSql('CREATE INDEX IDX_61B90312F6B64335 ON frase (direzione_id)');
        $this->addSql('CREATE INDEX IDX_61B90312B291EE38 ON frase (livello_id)');
        $this->addSql('CREATE INDEX IDX_61B90312D4B622DE ON frase (espressione_id)');
        $this->addSql('CREATE TABLE lingua (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, descrizione VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE livello (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, descrizione VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE traduzione (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, frase_id INTEGER NOT NULL, espressione_id INTEGER NOT NULL, CONSTRAINT FK_8A4BED62E073EAA4 FOREIGN KEY (frase_id) REFERENCES frase (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8A4BED62D4B622DE FOREIGN KEY (espressione_id) REFERENCES espressione (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8A4BED62E073EAA4 ON traduzione (frase_id)');
        $this->addSql('CREATE INDEX IDX_8A4BED62D4B622DE ON traduzione (espressione_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE contesto');
        $this->addSql('DROP TABLE direzione');
        $this->addSql('DROP TABLE espressione');
        $this->addSql('DROP TABLE frase');
        $this->addSql('DROP TABLE lingua');
        $this->addSql('DROP TABLE livello');
        $this->addSql('DROP TABLE traduzione');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
