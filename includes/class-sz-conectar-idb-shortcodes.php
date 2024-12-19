<?php

class Sz_Conectar_Idb_Shortcodes {

    public static function init() {
        add_shortcode('gerar_charada', [self::class, 'gerar_charada_shortcode']);
    }

    public static function gerar_charada_shortcode() {
        ob_start();
        ?>
        <form id="charada-form">
            <div id="charada-container">
                <p>Carregando charada...</p>
            </div>
            <div id="loading-message" style="display: none;">Verificando a resposta...</div>
            <div id="result-message"></div>
        </form>
        <?php
        return ob_get_clean();
    }

    public static function gerar_charada_ajax() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nome_da_tabela_atual'; // Nome da tabela atual

        $grupo_id = intval($_POST['grupo_id']);
        $charada = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE grupo_id = %d ORDER BY RAND() LIMIT 1",
            $grupo_id
        ));

        if ($charada) {
            $output = '<input type="hidden" name="charada_id" value="' . esc_attr($charada->id) . '">';
            $output .= '<p><strong>Charada:</strong> ' . esc_html($charada->pergunta) . '</p>';
            $output .= '<input type="text" name="form_fields[resposta]" placeholder="Resposta" required>';
            $output .= '<button type="submit">Enviar</button>';
            wp_send_json_success($output);
        } else {
            wp_send_json_error('Nenhuma charada disponível.');
        }
    }

    public static function validar_charada_resposta() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nome_da_tabela_atual';

        $charada_id = intval($_POST['charada_id']);
        $resposta = sanitize_text_field($_POST['resposta']);
        $charada = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND resposta = %s",
            $charada_id,
            $resposta
        ));

        if ($charada) {
            wp_send_json_success('Resposta correta!');
        } else {
            wp_send_json_error('Resposta incorreta.');
        }
    }
}
