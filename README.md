# 📋 Task Manager (Корпоративная Доска Задач)

> Современный менеджер задач с канбан-доской, очередями, кэшированием и тестами.

[![PHP Version](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Tests](https://img.shields.io/badge/tests-✔️%20passed-brightgreen)]()
[![GitHub repo size](https://img.shields.io/github/repo-size/nizamovedik/laravel_ai)](https://github.com/nizamovedik/laravel_ai)
[![GitHub commit activity](https://img.shields.io/github/commit-activity/t/nizamovedik/laravel_ai)](https://github.com/nizamovedik/laravel_ai/commits/main)

---

## 🚀 О проекте

**Task Manager** — это полноценное SPA-приложение для управления задачами и проектами, разработанное с нуля. Проект создан для демонстрации навыков современной веб-разработки и следует принципам чистой архитектуры.

**Ключевые возможности:**

- ✅ **Управление проектами и задачами** (полный CRUD)
- 📋 **Канбан-доска** с Drag-and-Drop (перетаскивание между статусами)
- 🏷️ **Теги**, **статусы** и **приоритеты** задач
- 💬 **Комментарии** с полиморфными связями
- ⚡ **Асинхронные очереди** (Redis) для уведомлений и отчётов
- 🧹 **Чистая архитектура** (Service → Repository → Controller)
- 📊 **Генерация отчётов** по проектам (CSV)
- 🔐 **JWT-авторизация** (Laravel Sanctum)
- 🧪 **Unit + Feature тесты** (Pest)
- 🎨 **Современный UI** на Vue 3 + Tailwind CSS

---

## 🏗️ Архитектура

Проект построен на принципах **чистой архитектуры** с чётким разделением ответственности:

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Vue 3 + Pinia + Vue Router + Axios
- **Styling:** Tailwind CSS + Heroicons
- **Database:** MySQL 8.4
- **Cache & Queues:** Redis
- **Infrastructure:** Docker, GitHub Actions (CI/CD)

---

## 📦 Установка (локально)

### 1. Клонировать репозиторий

```bash
git clone https://github.com/nizamovedik/laravel_ai.git
cd laravel_ai

📄 Лицензия
MIT © Enizamov

🙌 Благодарности
[Laravel](https://laravel.com/) — за надёжный и элегантный PHP-фреймворк.
[Vue.js](https://vuejs.org/) — за реактивность и удобство работы с интерфейсом.
[Tailwind CSS](https://tailwindcss.com/) — за чистый и быстрый стиль без лишнего CSS.
[Redis](https://redis.io/) — за скорость и гибкость в кешировании и очередях.
[Docker](https://www.docker.com/) — за контейнеризацию и упрощение деплоя.