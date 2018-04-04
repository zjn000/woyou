define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'coupon/usercoupon/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: '',
                    table: 'user_coupon',
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
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'uid', title: __('Uid')},
                        {field: 'user.phone', title: __('Phone')},
                        {field: 'coupon.name', title: __('Name'),operate: 'LIKE'},
                        {field: 'face_value', title: __('Face Value'),operate: false},
                        {field: 'type_name', title: __('Type'),operate: false},
                        {field: 'effective_time', title: __('Effective_time'),operate: false},
                        {field: 'create_time', title: __('Create_time'),operate: false, formatter: Table.api.formatter.datetime},
                        {field: 'is_use', title: __('Is_use'),operate: false,formatter: Controller.api.formatter.is_use},
                        {field: 'usage_time', title: __('Usage_time'),operate: false, formatter: Table.api.formatter.datetime},
                        {field: 'o_no', title: __('O_no'),operate: false}
                    ]
                ],
                search:false
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
                is_use: function (value, row, index) {

                    var color = '';
                    var status_text = '';
                    if(row.is_use == 1){
                        color = 'success';
                        status_text = __('Used');
                    }
                    if(row.is_use == 0){
                        color = 'warning'
                        status_text = __('Unused');
                    }
                    //渲染状态
                    var html = '<span class="text-' + color + '">'+status_text+'</span>';
                    return html;

                }
            }
        }
    };
    return Controller;
});