export function up (sequelize, app) {
  const { Sequelize } = sequelize
  const queryInterface = sequelize.getQueryInterface()

  return queryInterface.createTable('user', {
    id: {
      type: Sequelize.INTEGER,
      primaryKey: true,
      autoIncrement: true
    },
    companyId: {
      type: Sequelize.INTEGER,
      references: {
        model: 'company',
        key: 'id'
      },
      onUpdate: 'CASCADE',
      onDelete: 'CASCADE'
    },
    deviceUserId: {
      type: Sequelize.INTEGER
    },
    email: {
      type: Sequelize.STRING,
      allowNull: false,
      unique: true
    },
    password: {
      type: Sequelize.STRING,
      allowNull: false
    },
    createdAt: {
      type: Sequelize.DATE
    },
    updatedAt: {
      type: Sequelize.DATE
    }
  })
}

export function down (sequelize) {
  const queryInterface = sequelize.getQueryInterface()

  return queryInterface.dropTable('user')
}
