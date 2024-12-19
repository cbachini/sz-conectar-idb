<?php

class Sz_Conectar_Idb_Riddles {
    public static function init() {
        add_action('wp_ajax_gerar_charada_ajax', [self::class, 'gerar_charada_ajax']);
        add_action('wp_ajax_nopriv_gerar_charada_ajax', [self::class, 'gerar_charada_ajax']);
        add_action('wp_ajax_validar_charada_resposta', [self::class, 'validar_charada_resposta']);
        add_action('wp_ajax_nopriv_validar_charada_resposta', [self::class, 'validar_charada_resposta']);
    }

    public static function gerar_charada_ajax() {
        global $wpdb;

        $grupo_id = isset($_POST['grupo_id']) ? intval($_POST['grupo_id']) : 0;
        if ($grupo_id) {
            $table_name = $wpdb->prefix . 'sz_access_phrases';
            $charada = $wpdb->get_row($wpdb->prepare(
                "SELECT id, pergunta FROM $table_name WHERE grupo_id = %d ORDER BY RAND() LIMIT 1",
                $grupo_id
            ));

            if ($charada) {
                wp_send_json_success(['id' => $charada->id, 'pergunta' => $charada->pergunta]);
            } else {
                wp_send_json_error(__('Nenhuma charada disponível.', 'sz-conectar-idb'));
            }
        } else {
            wp_send_json_error(__('Grupo não especificado.', 'sz-conectar-idb'));
        }
    }

    public static function validar_charada_resposta() {
        global $wpdb;

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $resposta = isset($_POST['resposta']) ? sanitize_text_field($_POST['resposta']) : '';

        if ($id && $resposta) {
            $table_name = $wpdb->prefix . 'sz_access_phrases';
            $charada = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d AND resposta = %s",
                $id,
                $resposta
            ));

            if ($charada) {
                wp_send_json_success(__('Resposta correta.', 'sz-conectar-idb'));
            } else {
                wp_send_json_error(__('Resposta incorreta.', 'sz-conectar-idb'));
            }
        } else {
            wp_send_json_error(__('Dados insuficientes.', 'sz-conectar-idb'));
        }
    }
}
