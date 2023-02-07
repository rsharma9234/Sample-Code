import { migrations } from '../app'

let target = {}
let to = process.argv[2] || ''

if (!isNaN(parseInt(to))) {
  to = parseInt(to)
}
if (to !== '') {
  target = { to }
}

migrations.down(target).then((revertedMigrations) => {
  if (revertedMigrations.length > 0) {
    console.log(`reverted migration(s): ${revertedMigrations.map((m) => m.file)}`)
  } else {
    console.log('0 migrations reverted')
  }

  process.exit(0)
})
