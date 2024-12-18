<?php
/**
 * Partials for the "Frases de Acesso" admin page.
 *
 * @package Sz_Conectar_IDB
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Verifica se o usuário tem permissão para acessar a página
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'sz-conectar-idb'));
}

// Inicializa a variável $frases
$frases = [];

// Conexão com o banco de dados
global $wpdb;
$table_name = $wpdb->prefix . 'frases_acesso';

// Consulta ao banco de dados
$frases = $wpdb->get_results("SELECT * FROM $table_name");

?>

<div class="wrap">
    <h1><?php echo esc_html(__('Frases de Acesso', 'sz-conectar-idb')); ?></h1>

    <!-- Formulário para adicionar uma nova frase -->
    <h2><?php echo esc_html(__('Adicionar Nova Frase de Acesso', 'sz-conectar-idb')); ?></h2>
    <form method="post" id="add-access-phrase-form">
        <?php wp_nonce_field('add_access_phrase', 'access_phrase_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="grupo_id"><?php echo esc_html(__('ID do Grupo', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="number" name="grupo_id" id="grupo_id" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="pergunta"><?php echo esc_html(__('Pergunta', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <textarea name="pergunta" id="pergunta" class="large-text" rows="3" required></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="resposta"><?php echo esc_html(__('Resposta', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="text" name="resposta" id="resposta" class="regular-text" required>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary"><?php echo esc_html(__('Adicionar Frase', 'sz-conectar-idb')); ?></button>
        </p>
    </form>

    <!-- Tabela de frases existentes -->
    <h2><?php echo esc_html(__('Frases de Acesso Existentes', 'sz-conectar-idb')); ?></h2>
    <table id="access-phrases-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php echo esc_html(__('ID', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('ID do Grupo', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Pergunta', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Resposta', 'sz-conectar-idb')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($frases)): ?>
                <?php foreach ($frases as $frase): ?>
                    <tr>
                        <td><?php echo esc_html($frase->id); ?></td>
                        <td><?php echo esc_html($frase->grupo_id); ?></td>
                        <td><?php echo esc_html($frase->pergunta); ?></td>
                        <td><?php echo esc_html($frase->resposta); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4"><?php echo esc_html(__('Nenhuma frase de acesso encontrada.', 'sz-conectar-idb')); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Inicialização do DataTables -->
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#access-phrases-table').DataTable({
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            }
        });
    });
</script>

<!-- Enfileiramento dos scripts e estilos do DataTables -->
<?php
function enqueue_datatables_assets() {
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_datatables_assets');
?>
