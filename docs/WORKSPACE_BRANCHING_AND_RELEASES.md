# GDW Core Branching and Release Guide

## Objetivo

Esta guia define la estrategia estable de ramas para GDW_Core y el flujo de releases para evitar confusiones entre Magento y PHP por linea.

## Ramas principales

| Rama | Scope Magento | Scope PHP | Composer package version | Composer constraint recomendado en modulos cliente | Tag pattern |
|---|---|---|---|---|---|
| `3.x` | Magento 2.3.x | PHP 7.x (legacy) | `3.0.x` | `^3.0` | `v3.0.x` |
| `4.x` | Magento 2.4.0 a 2.4.3 | PHP 7.4 | `4.0.x` | `^4.0` | `v4.0.x` |
| `4.4.x` | Magento 2.4.4+ | PHP 8.1+ | `4.4.x` | `^4.4` | `v4.4.x` |

## Reglas por linea

1. `3.x` y `4.x` deben mantenerse compatibles con sintaxis de PHP 7.4.
2. `4.4.x` puede usar sintaxis moderna de PHP 8.1+.
3. Cada release debe mantener alineados:
   - `composer.json` -> `version`
   - `etc/module.xml` -> `setup_version`
4. No mezclar features nuevas entre lineas sin evaluar compatibilidad.
5. Cambios de hotfix se aplican primero en la linea afectada y luego se backport/forward-port segun aplique.

## Flujo de release

### 1) Elegir linea

1. Bug en Magento 2.3.x -> release en `3.x`
2. Bug en Magento 2.4.0-2.4.3 -> release en `4.x`
3. Bug en Magento 2.4.4+ -> release en `4.4.x`

### 2) Subir patch version

Ejemplos:

- `3.0.2` en `3.x`
- `4.0.2` en `4.x`
- `4.4.1` en `4.4.x`

### 3) Validar antes de commit

1. Validar `composer.json`:

```bash
php -r 'json_decode(file_get_contents("composer.json"), true, 512, JSON_THROW_ON_ERROR); echo "composer ok\n";'
```

2. Validar `etc/module.xml`:

```bash
php -r '$x=simplexml_load_file("etc/module.xml"); if(!$x){exit(1);} echo "module.xml ok\n";'
```

### 4) Commit y tag

```bash
git add composer.json etc/module.xml
git commit -m "Bump version to X.Y.Z"
git tag vX.Y.Z
```

### 5) Push de rama y tag

```bash
git push origin <rama>
git push origin vX.Y.Z
```

## Matriz rapida para dependencias

1. Proyecto Magento 2.3.x -> `gdw/core:^3.0`
2. Proyecto Magento 2.4.0 a 2.4.3 -> `gdw/core:^4.0`
3. Proyecto Magento 2.4.4+ -> `gdw/core:^4.4`

## Nota operativa

Si una nueva version de Magento o PHP cambia la compatibilidad oficial, crear una nueva linea de rama/version en lugar de forzar compatibilidad en una linea existente.
