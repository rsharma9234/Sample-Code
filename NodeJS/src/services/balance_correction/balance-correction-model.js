export default function (sequelize) {
  const Sequelize = sequelize.Sequelize

  const balanceCorrection = sequelize.define('balanceCorrection', {
    date: {
      type: Sequelize.DATE,
      allowNull: false
    },
    type: {
      type: Sequelize.STRING,
      allowNull: false
    },
    comment: {
      type: Sequelize.TEXT
    },
    duration: {
      type: Sequelize.INTEGER,
      allowNull: false
    },
    leaveId: {
      type: Sequelize.INTEGER,
      allowNull: true
    }
  }, {
    freezeTableName: true,
    classMethods: {
      associate () {
        const { models } = sequelize

        this.belongsTo(models.company)
        this.belongsTo(models.terminal)
        this.belongsTo(models.user)
      }
    }
  })

  return balanceCorrection
}
