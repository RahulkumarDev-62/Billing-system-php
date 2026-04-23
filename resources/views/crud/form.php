<section>
    <h1><?php echo e((string) $title); ?></h1>
    <div class="card">
        <form action="<?php echo e(url($action)); ?>" method="post">
            <?php echo csrf_field(); ?>

            <?php foreach ($fields as $field): ?>
                <?php $value = $item[$field['name']] ?? ''; ?>
                <div class="field">
                    <label for="<?php echo e((string) $field['name']); ?>"><?php echo e((string) $field['label']); ?></label>
                    <?php if (($field['type'] ?? 'text') === 'select'): ?>
                        <select id="<?php echo e((string) $field['name']); ?>" name="<?php echo e((string) $field['name']); ?>">
                            <option value="">Select one</option>
                            <?php foreach (($field['options'] ?? []) as $option): ?>
                                <option value="<?php echo e((string) $option['value']); ?>" <?php echo ((string) $value === (string) $option['value']) ? 'selected' : ''; ?>>
                                    <?php echo e((string) $option['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif (($field['type'] ?? 'text') === 'checkbox'): ?>
                        <input id="<?php echo e((string) $field['name']); ?>" name="<?php echo e((string) $field['name']); ?>" type="checkbox" value="1" <?php echo !empty($value) ? 'checked' : ''; ?>>
                    <?php else: ?>
                        <input
                            id="<?php echo e((string) $field['name']); ?>"
                            name="<?php echo e((string) $field['name']); ?>"
                            type="<?php echo e((string) ($field['type'] ?? 'text')); ?>"
                            value="<?php echo e((string) $value); ?>"
                            <?php echo isset($field['step']) ? 'step="' . e((string) $field['step']) . '"' : ''; ?>
                        >
                    <?php endif; ?>

                    <?php if (!empty($errors[$field['name']])): ?>
                        <div class="error-text"><?php echo e((string) $errors[$field['name']]); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button class="btn" type="submit"><?php echo e((string) ($submitLabel ?? 'Save')); ?></button>
        </form>
    </div>
</section>
