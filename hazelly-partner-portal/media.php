<?php
/**
 * Hazelly Hair Love — Partner Portal
 * media.php — Media data layer (CRUD on data/media.json)
 */

require_once __DIR__ . '/config.php';

/* ── Read all ────────────────────────────────────────────────── */
function media_get_all(): array {
    if (!file_exists(MEDIA_FILE)) return [];
    $data = json_decode(file_get_contents(MEDIA_FILE), true);
    return is_array($data) ? $data : [];
}

/* ── Write all ───────────────────────────────────────────────── */
function media_save_all(array $items): bool {
    if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
    return file_put_contents(MEDIA_FILE, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

/* ── Get by type ─────────────────────────────────────────────── */
function media_get_by_type(string $type): array {
    if ($type === 'all') return media_get_all();
    return array_values(array_filter(media_get_all(), fn($i) => $i['type'] === $type));
}

/* ── Get by ID ───────────────────────────────────────────────── */
function media_get_by_id(string $id): ?array {
    foreach (media_get_all() as $item) {
        if ($item['id'] === $id) return $item;
    }
    return null;
}

/* ── Add ─────────────────────────────────────────────────────── */
function media_add(array $data): array {
    $items = media_get_all();
    $item  = array_merge([
        'id'          => 'item-' . uniqid(),
        'addedAt'     => date('c'),
        'is_new'      => true,
        'url'         => '',
        'file_name'   => '',
        'file_size'   => '',
        'description' => '',
    ], $data);
    array_unshift($items, $item);
    media_save_all($items);
    return $item;
}

/* ── Update ──────────────────────────────────────────────────── */
function media_update(string $id, array $updates): ?array {
    $items = media_get_all();
    foreach ($items as &$item) {
        if ($item['id'] === $id) {
            $item = array_merge($item, $updates);
            media_save_all($items);
            return $item;
        }
    }
    return null;
}

/* ── Delete ──────────────────────────────────────────────────── */
function media_delete(string $id): bool {
    $items = media_get_all();
    // Remove associated uploaded file if it exists
    foreach ($items as $item) {
        if ($item['id'] === $id && !empty($item['file_path'])) {
            $full_path = BASE_DIR . '/' . ltrim($item['file_path'], '/');
            if (file_exists($full_path)) @unlink($full_path);
        }
    }
    $filtered = array_values(array_filter($items, fn($i) => $i['id'] !== $id));
    return media_save_all($filtered);
}

/* ── Counts ──────────────────────────────────────────────────── */
function media_counts(): array {
    $items = media_get_all();
    return [
        'total' => count($items),
        'pdf'   => count(array_filter($items, fn($i) => $i['type'] === 'pdf')),
        'photo' => count(array_filter($items, fn($i) => $i['type'] === 'photo')),
        'video' => count(array_filter($items, fn($i) => $i['type'] === 'video')),
    ];
}

/* ── Search ──────────────────────────────────────────────────── */
function media_search(string $query, string $type = 'all'): array {
    $q     = mb_strtolower(trim($query));
    $items = media_get_by_type($type);
    if ($q === '') return $items;
    return array_values(array_filter($items, function ($item) use ($q) {
        return str_contains(mb_strtolower($item['title'] ?? ''), $q)
            || str_contains(mb_strtolower($item['description'] ?? ''), $q);
    }));
}
