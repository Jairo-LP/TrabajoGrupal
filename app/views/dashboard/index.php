<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Panel de Gestión de Citas</h3>
        <p class="text-muted small mb-0">Vista adaptada al rol: <strong><?= htmlspecialchars($usuario['rol']) ?></strong></p>
    </div>

    <?php if ($usuario['rol'] === 'cliente'): ?>
        <div class="d-flex gap-2">
            <!-- TODO [Sprint 2 - RF02]: Conectar este botón a un modal o vista para registrar mascota -->
            <button class="btn btn-outline-primary btn-sm" onclick="mostrarAlertaSprint('Sprint 2 - RF02: Formulario de nueva mascota')">
                <i class="bi bi-plus-circle me-1"></i> Nueva Mascota
            </button>
            <!-- TODO [Sprint 2 - RF03]: Conectar este botón a un modal o vista para agendar cita -->
            <button class="btn btn-primary btn-sm" onclick="mostrarAlertaSprint('Sprint 2 - RF03: Formulario para solicitar cita')">
                <i class="bi bi-calendar-plus me-1"></i> Solicitar Cita
            </button>
        </div>
    <?php endif; ?>
</div>

<?php if ($mensajeCita !== null): ?>
    <div class="alert alert-<?= htmlspecialchars($mensajeCita['tipo']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($mensajeCita['texto']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>

<?php if ($usuario['rol'] === 'cliente'): ?>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-paw me-1 text-primary"></i> Mis Mascotas Registradas
        </div>
        <div class="card-body">
            <?php if (empty($mascotas)): ?>
                <p class="text-muted mb-0">No tienes mascotas registradas aún.</p>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($mascotas as $m): ?>
                        <?php $estaCertificada = (int)($m['certificada'] ?? 0) === 1; ?>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold mb-0"><?= htmlspecialchars($m['nombre']) ?></h6>
                                        <?php if ($estaCertificada): ?>
                                            <span class="badge bg-success">Certificada</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Sin certificar</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($m['especie']) ?></span>
                                    <span class="small text-muted ms-1"><?= htmlspecialchars($m['raza']) ?> • <?= (int)$m['edad'] ?> años</span>
                                    <?php if ($estaCertificada && !empty($m['codigo_certificado'])): ?>
                                        <div class="small text-muted mt-2">
                                            Código: <strong><?= htmlspecialchars($m['codigo_certificado']) ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if ($estaCertificada): ?>
                                    <div class="mt-3">
                                        <a href="index.php?action=ver_certificado&id=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline-success w-100">
                                            Ver Certificado
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-semibold">
            Certificación de Mascotas
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mascota</th>
                        <th>Dueño</th>
                        <th>Estado</th>
                        <th>Código / Observación</th>
                        <th>Emitir / Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mascotas)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No hay mascotas registradas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($mascotas as $m): ?>
                            <?php $estaCertificada = (int)($m['certificada'] ?? 0) === 1; ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($m['nombre']) ?></strong>
                                    <div class="small text-muted"><?= htmlspecialchars($m['especie']) ?> • <?= htmlspecialchars($m['raza']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($m['nombre_dueno'] ?? '') ?></td>
                                <td>
                                    <?php if ($estaCertificada): ?>
                                        <span class="badge bg-success">Certificada</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($estaCertificada): ?>
                                        <div class="small fw-semibold"><?= htmlspecialchars($m['codigo_certificado']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($m['observacion_certificado'] ?? '') ?></div>
                                    <?php else: ?>
                                        <span class="small text-muted">Aún no certificada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" action="index.php?action=certificar_mascota" class="d-flex gap-2 align-items-center">
                                        <input type="hidden" name="id_mascota" value="<?= (int)$m['id'] ?>">
                                        <input type="text" name="observacion_certificado" class="form-control form-control-sm" placeholder="Observación clínica..." value="<?= htmlspecialchars($m['observacion_certificado'] ?? '') ?>" required>
                                        <button type="submit" class="btn btn-sm btn-primary text-nowrap">
                                            <?= $estaCertificada ? 'Reemitir' : 'Certificar' ?>
                                        </button>
                                        <?php if ($estaCertificada): ?>
                                            <a href="index.php?action=ver_certificado&id=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline-success text-nowrap">
                                                Ver
                                            </a>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-calendar-check me-1 text-primary"></i> Listado de Citas</span>
        <!-- TODO [Sprint 4]: Activar filtro dinámico en JS por estado -->
        <select id="filtroEstado" class="form-select form-select-sm w-auto">
            <option value="todas">Todos los estados</option>
            <option value="pendiente">Pendiente</option>
            <option value="confirmada">Confirmada</option>
            <option value="atendida">Atendida</option>
            <option value="cancelada">Cancelada</option>
        </select>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tablaCitas">
            <thead class="table-light">
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Mascota</th>
                    <?php if ($usuario['rol'] !== 'cliente'): ?>
                        <th>Dueño</th>
                    <?php endif; ?>
                    <th>Veterinario</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acciones / Nota</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($citas)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay citas registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($citas as $c): ?>
                        <?php
                            $badgeClass = match($c['estado']) {
                                'pendiente' => 'bg-warning text-dark',
                                'confirmada' => 'bg-info text-dark',
                                'atendida' => 'bg-success',
                                'cancelada' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        ?>
                        <tr data-estado="<?= htmlspecialchars($c['estado']) ?>">
                            <td><?= date('d/m/Y H:i', strtotime($c['fecha_hora'])) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($c['mascota']) ?></strong>
                                <div class="small text-muted"><?= htmlspecialchars($c['especie']) ?></div>
                            </td>
                            <?php if ($usuario['rol'] !== 'cliente'): ?>
                                <td><?= htmlspecialchars($c['dueno']) ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($c['veterinario']) ?></td>
                            <td><?= htmlspecialchars($c['motivo']) ?></td>
                            <td><span class="badge <?= $badgeClass ?> text-uppercase"><?= htmlspecialchars($c['estado']) ?></span></td>
                            <td>
                                <?php if ($usuario['rol'] === 'recepcionista' && in_array($c['estado'], ['pendiente', 'confirmada'], true)): ?>
                                    <form method="POST" action="index.php?action=cambiar_estado_cita" class="d-flex gap-1 flex-wrap">
                                        <input type="hidden" name="id_cita" value="<?= (int)$c['id'] ?>">
                                        <?php if ($c['estado'] === 'pendiente'): ?>
                                            <button type="submit" name="estado" value="confirmada" class="btn btn-sm btn-outline-success">Confirmar</button>
                                        <?php endif; ?>
                                        <button type="submit" name="estado" value="cancelada" class="btn btn-sm btn-outline-danger">Cancelar</button>
                                    </form>
                                <?php elseif ($usuario['rol'] === 'veterinario' && in_array($c['estado'], ['pendiente', 'confirmada'], true)): ?>
                                    <form method="POST" action="index.php?action=cambiar_estado_cita" class="d-flex gap-1 align-items-center">
                                        <input type="hidden" name="id_cita" value="<?= (int)$c['id'] ?>">
                                        <input type="hidden" name="estado" value="atendida">
                                        <input type="text" name="nota_clinica" class="form-control form-control-sm" placeholder="Nota clínica..." required>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Atender</button>
                                    </form>
                                <?php else: ?>
                                    <span class="small text-muted"><?= htmlspecialchars($c['nota_clinica'] ?? 'Sin notas') ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>