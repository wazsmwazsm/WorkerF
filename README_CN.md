# WorkerF 框架部分

## 关于

  一个基于 workerman 的 http 小型框架, 框架核心部分

  - 常驻内存
  - 多进程, 高并发
  - 单例的数据库连接
  - 使用依赖注入
  - 简洁的路由
  - 提供 mysql 驱动, 支持断线自动重连
  - 提供 redis 驱动, 基于 predis

  WorkerA 不是一个全面的、多功能的框架, 它很小, 只有一些最基础的功能。
  但是它高效、简介。通过 PSR-4 自动加载机制和自动依赖注入, 你可以尽可能的对其进行扩展。

## 依赖
  [workerman](http://www.workerman.net/ "workerman")

## License

The WorkerA is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
