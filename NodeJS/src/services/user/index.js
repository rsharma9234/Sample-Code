import service from 'feathers-sequelize'

import user from './user-model'
import hooks from './hooks'

export default function () {
  const app = this

  const options = {
    Model: user(app.get('sequelize')),
    paginate: {
      default: 5,
      max: 25
    }
  }

  app.use('/users', service(options))

  const userService = app.service('/users')

  userService.hooks(hooks)
};
