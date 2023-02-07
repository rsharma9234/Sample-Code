import Sequelize from 'sequelize'
import connector from './connector'
import signup from './signup'
import user from './user'
import timelog from './timelog'
import company from './company'
import holiday from './holiday'
import authentication from './authentication'
import terminal from './terminal'
import timeTemplate from './time_template'
import userTimeTemplate from './user_time_template'
import userState from './user_state'
import balance from './balance'
import graphql from './graphql'
import leave from './leave'
import balanceCorrection from './balance_correction'
import vacation from './vacation'

export default function () {
  const app = this
  const { database, username, password, options } = app.get('dbConfig')

  const sequelize = new Sequelize(
    database,
    username,
    password,
    options
  )

  app.set('sequelize', sequelize)

  app.configure(authentication)
    .configure(company)
    .configure(user)
    .configure(timeTemplate)
    .configure(userTimeTemplate)
    .configure(timelog)
    .configure(holiday)
    .configure(terminal)
    .configure(graphql)
    .configure(connector)
    .configure(signup)
    .configure(userState)
    .configure(balance)
    .configure(leave)
    .configure(balanceCorrection)
    .configure(vacation)

  // Beziehungen einrichten
  Object.keys(sequelize.models).forEach((modelName) => {
    if ('associate' in sequelize.models[modelName]) {
      sequelize.models[modelName].associate()
    }
  })
};
