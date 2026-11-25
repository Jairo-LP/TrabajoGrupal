<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Certificado Veterinario de Mascota</h3>
        <p class="text-muted small mb-0">Código oficial: <strong><?= htmlspecialchars($mascota['codigo_certificado']) ?></strong></p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            Imprimir / Guardar PDF
        </button>
        <a href="index.php?action=dashboard" class="btn btn-primary btn-sm">Volver al Panel</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
            <div>
                <span class="badge bg-success mb-2">CERTIFICADA</span>
                <h4 class="fw-bold mb-1"><?= htmlspecialchars($mascota['nombre']) ?></h4>
                <p class="text-muted mb-0">
                    <?= htmlspecialchars($mascota['especie']) ?> • <?= htmlspecialchars($mascota['raza']) ?> • <?= (int)$mascota['edad'] ?> años
                </p>
            </div>
            <div class="text-end">
                <div class="small text-muted">Fecha de emisión</div>
                <div class="fw-semibold"><?= date('d/m/Y H:i', strtotime($mascota['fecha_certificacion'])) ?></div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="border rounded p-3 bg-light">
                    <div class="small text-muted">Dueño / Propietario</div>
                    <div class="fw-semibold"><?= htmlspecialchars($mascota['nombre_dueno']) ?></div>
                    <?php if (!empty($mascota['email_dueno'])): ?>
                        <div class="small text-muted"><?= htmlspecialchars($mascota['email_dueno']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border rounded p-3 bg-light">
                    <div class="small text-muted">Emitido por</div>
                    <div class="fw-semibold"><?= htmlspecialchars($mascota['nombre_certificador'] ?? 'Personal autorizado') ?></div>
                    <div class="small text-muted">Código: <?= htmlspecialchars($mascota['codigo_certificado']) ?></div>
                </div>
            </div>
        </div>

        <div class="border rounded p-3">
            <div class="small text-muted mb-1">Observación clínica / Validación</div>
            <p class="mb-0"><?= nl2br(htmlspecialchars($mascota['observacion_certificado'] ?? '')) ?></p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>