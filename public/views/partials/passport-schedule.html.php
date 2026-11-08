<?php
/**
 * Harmonogram okazu na najbliższe 7 dni: zadania z reguł pielęgnacji i dodane ze skanów, z odhaczaniem.
 * Wymaga: $tree, $treeUrl, $treeSchedule (dni z zadaniami), $e. Odświeżana przez specimen.js ([data-tree-schedule]).
 */
use App\Service\ScheduleService;

$typeIcons = [ScheduleService::TYPE_WATER => 'water_drop', ScheduleService::TYPE_SHAPE => 'content_cut', ScheduleService::TYPE_CARE => 'spa'];
$taskCount = array_sum(array_map(static fn (array $day): int => count($day['tasks']), $treeSchedule));
?>
<section class="passport-schedule" id="passport-schedule" aria-labelledby="passport-schedule-title" data-tree-schedule>
    <header class="passport-section-header">
        <h2 class="passport-section-header__title" id="passport-schedule-title">Harmonogram okazu</h2>
        <a href="/schedule" class="passport-schedule__all">Cały harmonogram</a>
    </header>

    <?php if ($treeSchedule === []): ?>
        <p class="passport-empty">Na najbliższe 7 dni nie ma zadań dla tego okazu. Kroki ze skanu dodasz tu przyciskiem „Dodaj kroki do harmonogramu”.</p>
    <?php else: ?>
        <p class="passport-schedule__lead"><?= $taskCount ?> <?= $e(\App\Support\PolishDate::plural($taskCount, 'zadanie', 'zadania', 'zadań')) ?> w ciągu 7 dni.</p>
        <?php foreach ($treeSchedule as $day): ?>
            <div class="passport-schedule__day">
                <h3 class="passport-schedule__date<?= $day['isToday'] ? ' is-today' : '' ?>">
                    <a href="/schedule?date=<?= $e($day['date']) ?>"><?= $e($day['label']) ?></a>
                </h3>
                <ul class="passport-schedule__tasks">
                    <?php foreach ($day['tasks'] as $task): ?>
                        <li class="passport-schedule__task<?= $task['done'] ? ' is-done' : '' ?>">
                            <form action="/schedule/tasks/done" method="POST" class="passport-schedule__form" data-tree-task>
                                <?php include __DIR__ . '/csrf.html.php'; ?>
                                <input type="hidden" name="id" value="<?= $e($task['id']) ?>">
                                <input type="hidden" name="date" value="<?= $e($task['date']) ?>">
                                <input type="hidden" name="_back" value="<?= $treeUrl ?>#passport-schedule">
                                <input type="hidden" name="done" value="0">
                                <label class="passport-schedule__hit">
                                    <input class="passport-schedule__check" type="checkbox" name="done" value="1" aria-label="Oznacz jako wykonane: <?= $e($task['title']) ?>"<?= $task['done'] ? ' checked' : '' ?>>
                                </label>
                                <div class="passport-schedule__body">
                                    <p class="passport-schedule__when">
                                        <span class="ms" aria-hidden="true"><?= $e($typeIcons[$task['type']] ?? 'spa') ?></span>
                                        <?= $e(ScheduleService::TYPE_LABELS[$task['type']] ?? '') ?>, <?= $e($task['time']) ?>
                                        <?php if ($task['urgent']): ?><strong class="passport-schedule__urgent">Pilne</strong><?php endif; ?>
                                    </p>
                                    <p class="passport-schedule__title"><?= $e($task['title']) ?></p>
                                    <?php if ($task['text'] !== ''): ?>
                                        <p class="passport-schedule__text"><?= $e($task['text']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="btn btn--tonal btn--sm passport-schedule__save">Zapisz</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
