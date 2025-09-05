import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: '/posts',
    },
    {
        title: 'Create',
        href: '/posts/create',
    },
];

type PostForm = {
    name: string;
    slug: string;
    content: string;
    category: string;
};

export default function CreatePost() {
    const { data, setData, post, errors, processing, reset } = useForm<PostForm>({
        name: '',
        slug: '',
        content: '',
        category: '',
    });

    // Auto-generate slug from title when user leaves the title field
    const handleTitleBlur = () => {
        if (data.name && !data.slug) {
            const generatedSlug = data.name
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            setData('slug', generatedSlug);
        }
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('posts.store'), {
            onSuccess: () => reset(),
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Post" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center gap-4">
                    <Link href="/posts">
                        <Button variant="outline" size="icon">
                            <ArrowLeft className="h-4 w-4" />
                        </Button>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">Create New Post</h1>
                        <p className="text-muted-foreground">Write and publish a new blog post</p>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Form */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Post Details</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={submit} className="space-y-6">
                                <div className="space-y-2">
                                    <Label htmlFor="name">Title</Label>
                                    <Input
                                        id="name"
                                        type="text"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        onBlur={handleTitleBlur}
                                        placeholder="Enter post title..."
                                        aria-invalid={!!errors.name}
                                    />
                                    <InputError message={errors.name} />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="slug">Slug *</Label>
                                    <Input
                                        id="slug"
                                        type="text"
                                        value={data.slug}
                                        onChange={(e) => setData('slug', e.target.value)}
                                        placeholder="URL slug (required)"
                                        required
                                        aria-invalid={!!errors.slug}
                                    />
                                    <InputError message={errors.slug} />
                                    <p className="text-sm text-muted-foreground">
                                        Auto-generated when you finish typing the title
                                    </p>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="category">Category</Label>
                                    <Input
                                        id="category"
                                        type="text"
                                        value={data.category}
                                        onChange={(e) => setData('category', e.target.value)}
                                        placeholder="Enter category..."
                                        aria-invalid={!!errors.category}
                                    />
                                    <InputError message={errors.category} />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="content">Content</Label>
                                    <textarea
                                        id="content"
                                        value={data.content}
                                        onChange={(e) => setData('content', e.target.value)}
                                        placeholder="Write your post content..."
                                        rows={12}
                                        className="border-input file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive flex w-full min-w-0 resize-none rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                        aria-invalid={!!errors.content}
                                    />
                                    <InputError message={errors.content} />
                                </div>

                                <div className="flex items-center gap-4">
                                    <Button type="submit" disabled={processing}>
                                        {processing ? 'Creating...' : 'Create Post'}
                                    </Button>

                                    <Link href="/posts">
                                        <Button type="button" variant="outline">
                                            Cancel
                                        </Button>
                                    </Link>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    {/* Preview */}
                    <Card className="lg:sticky lg:top-4 lg:h-fit">
                        <CardHeader>
                            <CardTitle className="text-base">Preview</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <h3 className="text-lg font-semibold">{data.name || 'Untitled Post'}</h3>
                                {data.category && <div className="text-muted-foreground text-sm">Category: {data.category}</div>}
                                <div className="prose prose-neutral dark:prose-invert max-w-none">
                                    <div className="text-foreground text-sm whitespace-pre-wrap">{data.content || 'No content yet...'}</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
