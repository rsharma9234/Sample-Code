import path from 'path'
import fs from 'fs'
import moment from 'moment'

const now = moment().format('YYYYMMDDHHmmss')

fs.createReadStream(
  path.resolve('src', 'migrations', 'template.js')
)
.pipe(
  fs.createWriteStream(
    path.resolve('src', 'migrations', `${now}__${process.argv[2] || 'migration'}.js`)
  )
)
