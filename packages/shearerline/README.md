# Shearerline - 在线课程教务系统扩展包

Shearerline 是一个基于 Laravel 的在线课程教务系统扩展包，提供完整的课程、课时、学生、教师和报名管理功能。

## 功能特性

- 课程管理 (CRUD)
- 课时管理 (CRUD)
- 学生管理 (CRUD)
- 教师管理 (CRUD)
- 报名管理 (CRUD)
- 数据统计面板
- RESTful API 接口
- Web 管理界面 (Blade 模板)
- Vue 组件支持
- 多语言支持 (中文/英文)
- 灵活的配置选项
- 事件驱动架构
- 数据迁移和数据填充

## 系统要求

- PHP >= 8.1
- Laravel >= 9.0
- MySQL >= 5.7 或 MariaDB >= 10.3

## 安装

### 1. 通过 Composer 安装

```bash
composer require shearerline/shearerline
```

### 2. 发布资源文件

```bash
php artisan shearerline:install
```

或者手动发布:

```bash
php artisan vendor:publish --tag=shearerline-config
php artisan vendor:publish --tag=shearerline-migrations
```

### 3. 运行数据库迁移

```bash
php artisan migrate
```

### 4. (可选) 填充测试数据

```bash
php artisan db:seed --class=ShearerlineDatabaseSeeder
```

## 配置

配置文件位于 `config/shearerline.php`，可配置项:

```php
return [
    'name' => env('SHEARERLINE_NAME', 'Shearerline'),
    'route_prefix' => env('SHEARERLINE_ROUTE_PREFIX', 'shearerline'),
    'route_middleware' => ['web'],
    'api_route_prefix' => env('SHEARERLINE_API_PREFIX', 'api/shearerline'),
    'api_middleware' => ['api'],
    'pagination' => [
        'per_page' => env('SHEARERLINE_PER_PAGE', 15),
    ],
    // ...
];
```

## 使用

### Web 界面

访问 `https://your-domain.com/shearerline` 进入管理后台。

### Facade 使用

```php
use Shearerline\Facades\Shearerline;

// 获取课程列表
$courses = Shearerline::getCourses(['keyword' => 'Laravel']);

// 创建课程
$course = Shearerline::createCourse([
    'title' => 'Laravel 入门教程',
    'teacher_id' => 1,
    'price' => 99.00,
    'status' => 'published',
]);

// 获取学生列表
$students = Shearerline::getStudents();

// 学生报名
$enrollment = Shearerline::enrollStudent([
    'course_id' => 1,
    'student_id' => 1,
]);
```

### 辅助函数

```php
// 获取 Shearerline 实例
$shearerline = shearerline();

// 格式化价格
echo format_price(99.00); // ¥99.00

// 格式化时长
echo format_duration(125); // 2小时5分钟
```

### API 接口

所有 API 接口前缀默认: `api/shearerline`

| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/statistics` | 获取统计数据 |
| GET | `/courses` | 获取课程列表 |
| POST | `/courses` | 创建课程 |
| GET | `/courses/{id}` | 获取课程详情 |
| PUT | `/courses/{id}` | 更新课程 |
| DELETE | `/courses/{id}` | 删除课程 |
| GET | `/courses/{id}/statistics` | 获取课程统计 |
| GET | `/courses/{id}/lessons` | 获取课时列表 |
| POST | `/courses/{id}/lessons` | 创建课时 |
| GET | `/lessons/{id}` | 获取课时详情 |
| PUT | `/lessons/{id}` | 更新课时 |
| DELETE | `/lessons/{id}` | 删除课时 |
| GET | `/students` | 获取学生列表 |
| POST | `/students` | 创建学生 |
| GET | `/students/{id}` | 获取学生详情 |
| PUT | `/students/{id}` | 更新学生 |
| DELETE | `/students/{id}` | 删除学生 |
| GET | `/teachers` | 获取教师列表 |
| POST | `/teachers` | 创建教师 |
| GET | `/teachers/{id}` | 获取教师详情 |
| PUT | `/teachers/{id}` | 更新教师 |
| DELETE | `/teachers/{id}` | 删除教师 |
| GET | `/enrollments` | 获取报名列表 |
| POST | `/enrollments` | 创建报名 |
| GET | `/enrollments/{id}` | 获取报名详情 |
| PUT | `/enrollments/{id}` | 更新报名 |
| DELETE | `/enrollments/{id}` | 删除报名 |
| POST | `/enrollments/{id}/cancel` | 取消报名 |

### Vue 组件

发布 Vue 组件:

```bash
php artisan vendor:publish --tag=shearerline-assets
```

可用组件:

- `CourseCard.vue` - 课程卡片组件
- `StatisticsPanel.vue` - 统计面板组件
- `DataTable.vue` - 数据表格组件

## 目录结构

```
packages/shearerline/
├── src/
│   ├── Console/Commands/       # Artisan 命令
│   ├── Contracts/             # 接口定义
│   ├── Events/                # 事件类
│   ├── Exceptions/          # 异常类
│   ├── Facades/               # Facade 门面
│   ├── Http/
│   │   ├── Controllers/       # 控制器
│   │   ├── Middleware/       # 中间件
│   │   └── Requests/         # 表单请求
│   ├── Jobs/                  # 队列任务
│   ├── Listeners/             # 事件监听器
│   ├── Mail/                  # 邮件类
│   ├── Models/                # Eloquent 模型
│   ├── Notifications/         # 通知类
│   ├── Policies/              # 授权策略
│   ├── Traits/                # Trait 特性
│   ├── config/                # 配置文件
│   ├── database/
│   │   ├── migrations/       # 数据库迁移
│   │   └── seeds/          # 数据填充
│   ├── resources/
│   │   ├── js/components/  # Vue 组件
│   │   ├── lang/             # 多语言文件
│   │   └── views/            # Blade 视图
│   ├── routes/                # 路由文件
│   ├── Tests/                 # 测试文件
│   ├── helpers.php           # 辅助函数
│   ├── Shearerline.php        # 核心类
│   └── ShearerlineServiceProvider.php  # 服务提供者
├── composer.json
└── README.md
```

## 开发

### 运行测试

```bash
vendor/bin/phpunit
```

### 事件

可用事件:

- `Shearerline\Events\StudentEnrolled` - 学生报名成功
- `Shearerline\Events\CourseCompleted` - 课程完成

## 许可证

MIT License
