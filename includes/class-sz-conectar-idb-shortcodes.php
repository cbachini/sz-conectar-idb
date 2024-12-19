function gerar_charada_shortcode() {
    ob_start();
    ?>
    <form id="charada-form">
        <div id="charada-container">
            <p><?php _e('Carregando charada...', 'sz-conectar-idb'); ?></p>
        </div>
        <div id="loading-message" style="display: none;"><?php _e('Verificando a resposta...', 'sz-conectar-idb'); ?></div>
        <div id="result-message"></div>
        <div id="audiobook" style="display: none;"><?php _e('Audiobook liberado!', 'sz-conectar-idb'); ?></div>
        <div id="ebook" style="display: none;"><?php _e('Ebook liberado!', 'sz-conectar-idb'); ?></div>
        <div id="aviso-bloqueio"><?php _e('Conteúdo bloqueado.', 'sz-conectar-idb'); ?></div>
    </form>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            const grupoElement = document.getElementById('numero-livro');
            if (grupoElement) {
                const grupoId = grupoElement.innerText.trim();

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'gerar_charada_ajax',
                        grupo_id: grupoId
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#charada-container').html(`
                                <input type="hidden" name="id" value="${response.data.id}">
                                <p><strong><?php _e('Charada:', 'sz-conectar-idb'); ?></strong> ${response.data.pergunta}</p>
                                <input size="1" type="text" name="resposta" placeholder="<?php _e('Resposta', 'sz-conectar-idb'); ?>" required>
                                <button type="submit" class="button-primary"><?php _e('Enviar', 'sz-conectar-idb'); ?></button>
                            `);
                        } else {
                            $('#charada-container').html('<p><?php _e('Nenhuma charada disponível para este grupo.', 'sz-conectar-idb'); ?></p>');
                        }
                    },
                    error: function () {
                        $('#charada-container').html('<p><?php _e('Erro ao carregar a charada. Tente novamente mais tarde.', 'sz-conectar-idb'); ?></p>');
                    }
                });
            }

            $('#charada-form').on('submit', function (e) {
                e.preventDefault();

                $('#loading-message').show();
                $('#result-message').html('');

                const formData = $(this).serialize();

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#loading-message').hide();

                        if (response.success) {
                            $('#result-message').html('<p style="color: green;"><?php _e('Resposta correta!', 'sz-conectar-idb'); ?></p>');
                            $('#audiobook').show();
                            $('#ebook').show();
                            $('#aviso-bloqueio').hide();
                        } else {
                            $('#result-message').html('<p style="color: red;"><?php _e('Resposta incorreta. Tente novamente.', 'sz-conectar-idb'); ?></p>');
                        }
                    },
                    error: function () {
                        $('#loading-message').hide();
                        $('#result-message').html('<p style="color: red;"><?php _e('Erro ao verificar a resposta. Tente novamente.', 'sz-conectar-idb'); ?></p>');
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('gerar_charada', 'gerar_charada_shortcode');
