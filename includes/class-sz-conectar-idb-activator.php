<?php

/**
 * Fired during plugin activation
 *
 * @link       https://soyuz.com.br
 * @since      1.0.0
 *
 * @package    Sz_Conectar_Idb
 * @subpackage Sz_Conectar_Idb/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sz_Conectar_Idb
 * @subpackage Sz_Conectar_Idb/includes
 * @author     Soyuz <info@soyuz.com.br>
 */
class Sz_Conectar_Idb_Activator {

    /**
     * Cria as tabelas no banco de dados e insere dados iniciais na ativação do plugin.
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;

        // Criação da tabela access_codes
        $table_name_access_codes = $wpdb->prefix . 'access_codes';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name_access_codes'") != $table_name_access_codes) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name_access_codes (
                id INT(11) NOT NULL AUTO_INCREMENT,
                school_name VARCHAR(255) NOT NULL,
                access_code VARCHAR(255) NOT NULL UNIQUE,
                max_uses INT(11) NOT NULL,
                current_uses INT(11) NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                PRIMARY KEY (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        // Criação da tabela frases_acesso
        $table_name_frases_acesso = $wpdb->prefix . 'frases_acesso';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name_frases_acesso'") != $table_name_frases_acesso) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name_frases_acesso (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                grupo_id mediumint(9) NOT NULL,
                pergunta text NOT NULL,
                resposta varchar(255) NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            // Inserção dos dados iniciais
            $initial_phrases = [
                ['grupo_id' => 1, 'pergunta' => 'Qual é a quinta palavra na página 6 do livro?', 'resposta' => 'ensolarado'],
                ['grupo_id' => 1, 'pergunta' => 'Qual é a nona palavra na página 12 do livro?', 'resposta' => 'direção'],
                ['grupo_id' => 1, 'pergunta' => 'Qual é a sétima palavra na página 21 do livro?', 'resposta' => 'quatro'],
                ['grupo_id' => 1, 'pergunta' => 'Qual é a primeira palavra na página 24 do livro?', 'resposta' => 'parabéns'],
                ['grupo_id' => 1, 'pergunta' => 'Qual é a segunda palavra na página 26 do livro?', 'resposta' => 'inclusiva'],
                ['grupo_id' => 2, 'pergunta' => 'Qual é a quarta palavra na página 6 do livro?', 'resposta' => 'goiabeira'],
                ['grupo_id' => 2, 'pergunta' => 'Qual é a décima palavra na página 12 do livro?', 'resposta' => 'descobriu'],
                ['grupo_id' => 2, 'pergunta' => 'Qual é a quarta palavra na página 16 do livro?', 'resposta' => 'gentis'],
                ['grupo_id' => 2, 'pergunta' => 'Qual é a terceira palavra na página 23 do livro?', 'resposta' => 'normalmente'],
                ['grupo_id' => 2, 'pergunta' => 'Qual é a oitava palavra na página 28 do livro?', 'resposta' => 'comer'],
                ['grupo_id' => 3, 'pergunta' => 'Qual é a terceira palavra na página 6 do livro?', 'resposta' => 'recreio'],
                ['grupo_id' => 3, 'pergunta' => 'Qual é a quinta palavra na página 17 do livro?', 'resposta' => 'final'],
                ['grupo_id' => 3, 'pergunta' => 'Qual é a terceira palavra na página 20 do livro?', 'resposta' => 'diversão'],
                ['grupo_id' => 3, 'pergunta' => 'Qual é a última palavra na página 22 do livro?', 'resposta' => 'investigação'],
                ['grupo_id' => 3, 'pergunta' => 'Qual é a segunda palavra na página 6 do livro?', 'resposta' => 'acaba'],
                ['grupo_id' => 4, 'pergunta' => 'Qual é a última palavra na página 8 do livro?', 'resposta' => 'mudar'],
                ['grupo_id' => 4, 'pergunta' => 'Qual é a quarta palavra na página 13 do livro?', 'resposta' => 'notícia'],
                ['grupo_id' => 4, 'pergunta' => 'Qual é a quinta palavra na página 18 do livro?', 'resposta' => 'pesquisei'],
                ['grupo_id' => 4, 'pergunta' => 'Qual é a terceira palavra na página 23 do livro?', 'resposta' => 'aprender'],
                ['grupo_id' => 4, 'pergunta' => 'Qual é a décima palavra na página 26 do livro?', 'resposta' => 'batman'],
                ['grupo_id' => 5, 'pergunta' => 'Qual é a segunda palavra na página 6 do livro?', 'resposta' => 'vermelho'],
                ['grupo_id' => 5, 'pergunta' => 'Qual é a nona palavra na página 15 do livro?', 'resposta' => 'balança'],
                ['grupo_id' => 5, 'pergunta' => 'Qual é a quinta palavra na página 20 do livro?', 'resposta' => 'firme'],
                ['grupo_id' => 5, 'pergunta' => 'Qual é a quinta palavra na página 26 do livro?', 'resposta' => 'sabe'],
                ['grupo_id' => 5, 'pergunta' => 'Qual é a última palavra na página 27 do livro?', 'resposta' => 'gracejos']
            ];

            foreach ($initial_phrases as $phrase) {
                $wpdb->insert(
                    $table_name_frases_acesso,
                    [
                        'grupo_id' => $phrase['grupo_id'],
                        'pergunta' => $phrase['pergunta'],
                        'resposta' => $phrase['resposta'],
                    ]
                );
            }
        }
    }
}
