<?php

class Sz_Conectar_Idb_Shortcodes {
    /**
     * Inicializa o shortcode
     */
    public static function init() {
        add_shortcode('gerar_charada', [self::class, 'gerar_charada_shortcode']);
    }

    /**
     * Renderiza o formulário de charadas
     */
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
}
