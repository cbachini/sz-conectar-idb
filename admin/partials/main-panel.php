<div class="wrap" style="font-family: Arial, sans-serif;">
    <h1 style="color: #FF8C00;">
        <?php echo esc_html__('Mixirica - Painel Principal', 'sz-conectar-idb'); ?>
    </h1>
    <p style="font-size: 16px;">
        <?php echo esc_html__('Bem-vindo ao Painel Principal. Aqui você pode acessar as funcionalidades do Mixirica rapidamente.', 'sz-conectar-idb'); ?>
    </p>

    <!-- Cards com Atalhos -->
    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <!-- Frases de Acesso -->
        <div style="flex: 1; padding: 20px; background: #FFF5E5; border: 1px solid #FF8C00; border-radius: 8px;">
            <h2 style="margin-top: 0;">
                <?php echo esc_html__('Frases de Acesso', 'sz-conectar-idb'); ?>
            </h2>
            <p><?php echo esc_html__('Gerencie frases de acesso utilizadas no sistema.', 'sz-conectar-idb'); ?></p>
            <a href="<?php echo admin_url('admin.php?page=frases_acesso'); ?>" class="button button-primary" style="background: #FF8C00; border-color: #FF8C00;">
                <?php echo esc_html__('Ir para Frases de Acesso', 'sz-conectar-idb'); ?>
            </a>
        </div>

        <!-- Códigos para o Professor -->
        <div style="flex: 1; padding: 20px; background: #E6F7FF; border: 1px solid #00A0D2; border-radius: 8px;">
            <h2 style="margin-top: 0;">
                <?php echo esc_html__('Códigos para o Professor', 'sz-conectar-idb'); ?>
            </h2>
            <p><?php echo esc_html__('Gerencie códigos exclusivos para professores.', 'sz-conectar-idb'); ?></p>
            <a href="<?php echo admin_url('admin.php?page=codigos_professor'); ?>" class="button button-primary" style="background: #00A0D2; border-color: #00A0D2;">
                <?php echo esc_html__('Ir para Códigos para o Professor', 'sz-conectar-idb'); ?>
            </a>
        </div>

        <!-- Códigos de Degustação -->
        <div style="flex: 1; padding: 20px; background: #E5FFEA; border: 1px solid #46B450; border-radius: 8px;">
            <h2 style="margin-top: 0;">
                <?php echo esc_html__('Códigos de Degustação', 'sz-conectar-idb'); ?>
            </h2>
            <p><?php echo esc_html__('Gerencie códigos temporários para degustação.', 'sz-conectar-idb'); ?></p>
            <a href="<?php echo admin_url('admin.php?page=codigos_degustacao'); ?>" class="button button-primary" style="background: #46B450; border-color: #46B450;">
                <?php echo esc_html__('Ir para Códigos de Degustação', 'sz-conectar-idb'); ?>
            </a>
        </div>
    </div>

    <!-- Listagem de Ações AJAX -->
    <div style="margin-top: 40px; padding: 20px; background: #F9F9F9; border: 1px solid #DDD; border-radius: 8px;">
        <h2 style="margin-top: 0;">
            <?php echo esc_html__('Ações AJAX Registradas', 'sz-conectar-idb'); ?>
        </h2>
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr>
                    <th style="text-align: left; border-bottom: 2px solid #CCC; padding: 10px;">
                        <?php echo esc_html__('Ação', 'sz-conectar-idb'); ?>
                    </th>
                    <th style="text-align: left; border-bottom: 2px solid #CCC; padding: 10px;">
                        <?php echo esc_html__('Privacidade', 'sz-conectar-idb'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wp_filter;
                
                $registered_actions = [];
                if (isset($wp_filter['wp_ajax'])) {
                    foreach ($wp_filter['wp_ajax'] as $key => $value) {
                        $registered_actions[] = ['action' => $key, 'privacy' => esc_html__('Somente Usuários Autenticados', 'sz-conectar-idb')];
                    }
                }

                if (isset($wp_filter['wp_ajax_nopriv'])) {
                    foreach ($wp_filter['wp_ajax_nopriv'] as $key => $value) {
                        $registered_actions[] = ['action' => $key, 'privacy' => esc_html__('Público', 'sz-conectar-idb')];
                    }
                }

                if (!empty($registered_actions)) {
                    foreach ($registered_actions as $registered_action) {
                        echo '<tr>
                            <td style="padding: 10px; border-bottom: 1px solid #EEE;">' . esc_html($registered_action['action']) . '</td>
                            <td style="padding: 10px; border-bottom: 1px solid #EEE;">' . esc_html($registered_action['privacy']) . '</td>
                        </tr>';
                    }
                } else {
                    echo '<tr>
                        <td colspan="2" style="padding: 10px; text-align: center; color: #666;">' . esc_html__('Nenhuma ação AJAX registrada.', 'sz-conectar-idb') . '</td>
                    </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
