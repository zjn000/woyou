define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/index',
                    add_url: 'user/add',
                    edit_url: 'user/edit',
                    del_url: 'user/del',
                    multi_url: 'user/multi',
                    table: 'user',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'openid', title: __('Openid'),operate: false},
                        {field: 'phone', title: __('Phone')},
                        {field: 'is_bind', title: __('Is_bind'),searchList:{"0":__('No'),"1":__('Yes')},formatter: Controller.api.formatter.is_bind},
                        {field: 'create_time', title: __('Create_time'),operate: false, formatter: Table.api.formatter.datetime},
                        {field: 'login_time', title: __('Login_time'),operate: false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'),searchList:{"1":__('Online'),"2":__('Downline')}, formatter: Controller.api.formatter.status},
                    ]
                ],
                search: false
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
                        status_text = __('Online');
                    }
                    if(row.status == 2){
                        color = 'danger'
                        status_text = __('Downline');
                    }
                    //渲染状态
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i>'+status_text+'</span>';
                    return html;

                },
                is_bind: function (value,row,index) {

                    var color = 'success';
                    var labal_text = __('Yes');

                    if(row.is_bind == 0){
                        color = 'danger'
                        labal_text = __('No');
                    }
                    //渲染状态
                    var html = '<span class="text-' + color + '">'+labal_text+'</span>';
                    return html;
                }


            }
        }
    };
    return Controller;
});