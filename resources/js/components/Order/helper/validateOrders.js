import _ from 'lodash'
export const validateOrders = (orders) => {
    let ordersError = []
    let hasErrors = true
    orders.forEach((order, index) => {
        if (order.id === 1)
            ordersError[index.toString()] = {
                brand_id: order.brand_id ? undefined : 'Selecione uma marca',
                brand_model: order.brand_model || order.brand_id === 1 ? null : 'Selecione um modelo',
                maintenance_info: {
                    issue: !_.isEmpty(order.maintenance_info.issue) && order.maintenance_info.issue.length >= 5 ?
                        null : order.maintenance_info.issue && order.maintenance_info.issue.length < 5 ? 'Descreva o problema com no mínimo 5 caracteres' : 'É obrigatório descrever o problema',
                }
            }
        hasErrors = ordersError[index] && (!!ordersError[index].maintenance_info.issue || !!ordersError[index].brand_id || !!ordersError[index].brand_model)

    })
    return { ordersError, hasErrors }
}
