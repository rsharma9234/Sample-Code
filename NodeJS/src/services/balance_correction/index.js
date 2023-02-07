import service from 'feathers-sequelize'

import balanceCorrectionModel from './balance-correction-model'
import hooks from './hooks'

export default function () {
  const app = this

  const options = {
    Model: balanceCorrectionModel(app.get('sequelize')),
    paginate: {
      default: 5,
      max: 25
    }
  }

  app.use('/balanceCorrections', service(options))

  const balanceCorrectionService = app.service('balanceCorrections')

  balanceCorrectionService.hooks(hooks)
}
