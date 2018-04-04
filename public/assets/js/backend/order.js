define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'order/multi',
                    table: 'order',
                }
            });

            var table = $("#table");
            var date = new Date();
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'order.id',
                exportOptions: {
                    fileName: 'order_' + date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate(),
                    ignoreColumn: [0, 'operate'], //默认不导出第一列(checkbox)与操作(operate)列
                },
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'uid', title: __('Uid')},
                        {field: 'shelves.name', title: __('Shelves_name'),operate: 'LIKE'},
                        {field: 'o_no', title: __('O_no')},
                        {field: 'all_total', title: __('All Total'),operate: false},
                        {field: 'discount', title: __('Discount'),operate: false},
                        {field: 'total', title: __('Total'),operate: false},
                        {field: 'status', title: __('Status'),searchList:{"1":__('Payment Successful'),"0":__('Failure')}, formatter: Controller.api.formatter.status},
                        {field: 'create_time', title: __('Create_time'),formatter: Table.api.formatter.datetime, operate: 'BETWEEN', type: 'datetime', addclass: 'datetimepicker', data: 'data-date-format="YYYY-MM-DD HH:mm:ss"'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [{
                                name: 'detail',
                                text: __('Order_detail'),
                                icon: 'fa fa-list',
                                classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                url: 'order/detail'
                            }],
                            formatter: Table.api.formatter.operate}
                    ]
                ],
                search:false
            });


            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                //这里可以获取从服务端获取的JSON数据
                // csv导出按钮事件
                $('#toolbar').on('click', '.btn-csv', function () {
                    window.location.href=data.export_url;
                });
            });


            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                status: function (value, row, index) {

                    var color = '';
                    var status_text = '';
                    if(row.status == 1){
                        color = 'success';
                        status_text = __('Payment Successful');
                    }
                    if(row.status == 0){
                        color = 'danger'
                        status_text = __('Failure');
                    }
                    //渲染状态
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i>'+status_text+'</span>';
                    return html;

                },
            }

        }
    };
    return Controller;
});