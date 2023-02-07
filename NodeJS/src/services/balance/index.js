import Service from './service'
import hooks from './hooks'

export default function () {
  const app = this

  app.use('/balance', new Service())
  app.service('balance').hooks(hooks)
}
