<x-cards.notification :notification="$notification"
                      :link="route('superadmin.companies.show', $notification->data['id'])"
                      :image="global_setting()->logo_url"
                      :title="$notification->data['company_name']"
                      :text="__('superadmin.newCompany.text')"
                      :time="$notification->created_at"
/>
