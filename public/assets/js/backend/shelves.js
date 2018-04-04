define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shelves/index',
                    add_url: 'shelves/add',
                    edit_url: 'shelves/edit',
                    multi_url: 'shelves/multi',

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
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'name', title: __('Name'),operate: 'LIKE', formatter: Controller.api.formatter.name},
                        {field: 'coding', title: __('Coding')},
                        {field: 'adds', title: __('Adds'),operate: false},
                        {field: 'principal', title: __('Principal'),operate: false},
                        {field: 'tell', title: __('Tell'),operate: false},
                        {field: 'bd_name', title: __('Bd'),operate: false},
                        {field: 'URL', title: __('URL'),operate: false},
                        {field: 'status', title: __('Status'),searchList:{"0":__('Not reviewed'),"1":__('Online'),"2":__('Downline')}, formatter: Controller.api.formatter.status},
                        {field: 'create_time', title: __('Create_time'),operate: false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table,events: Table.api.events.operate,formatter: Table.api.formatter.operate}
                    ]
                ],
                search: false,
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
                    if(row.status == 0){
                        color = 'info';
                        status_text = __('Not reviewed');
                    }
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
                name:function (value, row, index) {
                    //这里手动构造URL
                    url = "shelvesproduct/index?ids=" + row.id;
                    return '<a href="' + url + '" class="btn btn-info btn-xs addtabsit" title="' + __('Shelves_product') + '">' + value + '</a>';
                }
            },
        }
    };
    return Controller;
});