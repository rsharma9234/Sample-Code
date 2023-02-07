import { migrations } from '../app'

migrations.up().then((executedMigrations) => {
  console.log('finished migrations', executedMigrations.map((o) => o.file))

  process.exit(0)
})
