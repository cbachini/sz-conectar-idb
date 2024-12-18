<div class="wrap" style="font-family: Arial, sans-serif;">
    <h1 style="color: #FF8C00;"><?php echo esc_html__('Mixirica - Painel Principal', 'sz-conectar-idb'); ?></h1>
    <p style="font-size: 16px;">
        <?php echo esc_html__('Bem-vindo ao Painel Principal. Aqui você pode acessar as funcionalidades do Mixirica rapidamente.', 'sz-conectar-idb'); ?>
    </p>

    <!-- Cards com Atalhos -->
    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <!-- Frases de Acesso -->
        <div style="flex: 1; padding: 20px; background: #FFF5E5; border: 1px solid #FF8C00; border-radius: 8px;">
            <h2 style="margin-top: 0;"><?php echo esc_html__('Frases de Acesso', 'sz-conectar-idb'); ?></h2>
            <p><?php echo esc_html__('Gerencie frases de acesso utilizadas no sistema.', 'sz-conectar-idb'); ?></p>
            <a href="<?php echo admin_url('admin.php?page=frases_acesso'); ?>" class="button button-primary" style="background: #FF8C00; border-color: #FF8C00;">
                <?php echo esc_html__('Ir para Frases de Acesso', 'sz-conectar-idb'); ?>
            </a>
        </div>

        <!-- Códigos para o Professor -->
        <div style="flex: 1; padding: 20px; background: #E6F7FF; border: 1px solid #00A0D2; border-radius: 8px;">
            <h2 style="margin-top: 0;"><?php echo esc_html__('Códigos para o Professor', 'sz-conectar-idb'); ?></h2>
            <p><?php echo esc_html__('Gerencie códigos exclusivos para professores.', 'sz-conectar-idb'); ?></p>
            <a href="<?php echo admin_url('admin.php?page=codigos_professor'); ?>" class="button button-primary" style="background: #00A0D2; border-color: #00A0D2;">
                <?php echo esc_html__('Ir para Códigos para o Professor', 'sz-conectar-idb'); ?>
            </a>
        </div>

        <!-- Códigos de Degustação -->
        <div style="flex: 1; padding: 20px; background: #E5FFEA; border: 1px solid #46B450; border-radius: 8px;">
            <h2 style="margin-top: 0;"><?php echo esc_html__('Códigos de Degustação', 'sz-conectar-idb'); ?></h2>
            <p><?php echo esc_html__('Gerencie códigos temporários para degustação.', 'sz-conectar-idb'); ?></p>
            <a href="<?php echo admin_url('admin.php?page=codigos_degustacao'); ?>" class="button button-primary" style="background: #46B450; border-color: #46B450;">
                <?php echo esc_html__('Ir para Códigos de Degustação', 'sz-conectar-idb'); ?>
            </a>
        </div>
    </div>
</div>
