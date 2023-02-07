export function up (sequelize) {
  const { Sequelize } = sequelize
  const queryInterface = sequelize.getQueryInterface()

  return queryInterface.dropAllTables()
    .then(() => queryInterface.createTable('company', {
      id: {
        type: Sequelize.INTEGER,
        primaryKey: true,
        autoIncrement: true
      },
      name: {
        type: Sequelize.STRING,
        allowNull: false
      },
      createdAt: {
        type: Sequelize.DATE
      },
      updatedAt: {
        type: Sequelize.DATE
      }
    }))
}

export function down (sequelize) {
  const queryInterface = sequelize.getQueryInterface()

  return queryInterface.dropAllTables()
}
